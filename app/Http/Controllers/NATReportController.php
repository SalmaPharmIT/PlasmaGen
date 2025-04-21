<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class NATReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('nat-report-upload');
    }

    public function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $spreadsheet1 = IOFactory::load($request->file('file1')->getPathname());
            $spreadsheet2 = IOFactory::load($request->file('file2')->getPathname());
            
            $filename1 = $request->file('file1')->getClientOriginalName();
            $filename2 = $request->file('file2')->getClientOriginalName();

            $allSamples = [];
            $metadataBlocks = [];

            // Process first file
            foreach ($spreadsheet1->getAllSheets() as $sheet) {
                $result = $this->extractSamplesFromSheet($sheet, $filename1 . ' - ' . $sheet->getTitle());
                $allSamples = array_merge($allSamples, $result['samples']);
                $metadataBlocks[] = $result['metadata'];
            }

            // Process second file
            foreach ($spreadsheet2->getAllSheets() as $sheet) {
                $result = $this->extractSamplesFromSheet($sheet, $filename2 . ' - ' . $sheet->getTitle());
                $allSamples = array_merge($allSamples, $result['samples']);
                $metadataBlocks[] = $result['metadata'];
            }

            $groupedSamples = $this->groupSamples($allSamples);

            return view('nat-report-upload', [
                'metadataBlocks' => $metadataBlocks,
                'groupedSamples' => $groupedSamples
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error processing files: ' . $e->getMessage());
        }
    }

    private function extractSamplesFromSheet($sheet, $sourceLabel)
    {
        $samples = [];
        $metadata = [
            'source' => $sourceLabel,
            'Analyzer' => '',
            'Test' => '',
            'Flags' => '',
            'Test operator' => '',
            'Validated by' => '',
            'Validated on' => '',
            'Negative control (batch) ID' => ''
        ];

        $data = $sheet->toArray(null, true, true, false);

        // Parse metadata from top rows
        for ($i = 0; $i < 30; $i++) {
            $row = implode(' ', $data[$i] ?? []);
            $this->extractMetadata($row, $metadata);
        }

        // Process samples
        for ($i = 0; $i < count($data); $i++) {
            $row = $data[$i] ?? [];
            foreach ($row as $cell) {
                if ($this->isValidTubeId($cell)) {
                    $entry = $this->createSampleEntry($cell, $sourceLabel);
                    $entry = $this->extractMarkers($entry, $data, $i);
                    $entry = $this->extractMetadataFromSurrounding($entry, $data, $i);
                    $entry['result'] = $this->interpretResult($entry);
                    $samples[] = $entry;
                    break;
                }
            }
        }

        return ['samples' => $samples, 'metadata' => $metadata];
    }

    private function isValidTubeId($cell)
    {
        if (!is_string($cell)) return false;
        
        return preg_match('/MG\d{10}/', $cell) ||
               preg_match('/^[A-Z]{2}\/[A-Z]{2}\/\d{2}\/\d{4}\.?$/', trim($cell));
    }

    private function createSampleEntry($tubeId, $sourceLabel)
    {
        return [
            'tube_id' => $tubeId,
            'source' => $sourceLabel,
            'HIV' => '',
            'HBV' => '',
            'HCV' => '',
            'result' => '',
            'timestamp' => '',
            'operator' => '',
            'analyzer' => '',
            'flags' => ''
        ];
    }

    private function extractMarkers($entry, $data, $currentRow)
    {
        for ($j = 1; $j <= 5; $j++) {
            $nextRow = $data[$currentRow + $j] ?? null;
            if (!is_array($nextRow)) continue;

            foreach ($nextRow as $val) {
                if (!is_string($val)) continue;
                $val = strtoupper(trim($val));

                if (strpos($val, 'HIV:') === 0) {
                    $entry['HIV'] = trim(explode(':', $val)[1] ?? '');
                } elseif (strpos($val, 'HBV:') === 0) {
                    $entry['HBV'] = trim(explode(':', $val)[1] ?? '');
                } elseif (strpos($val, 'HCV:') === 0) {
                    $entry['HCV'] = trim(explode(':', $val)[1] ?? '');
                }
            }
        }
        return $entry;
    }

    private function extractMetadataFromSurrounding($entry, $data, $currentRow)
    {
        $surrounding = array_merge(
            $data[$currentRow - 2] ?? [],
            $data[$currentRow - 1] ?? [],
            $data[$currentRow + 1] ?? [],
            $data[$currentRow + 2] ?? []
        );

        foreach ($surrounding as $meta) {
            if (!is_string($meta)) continue;

            if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $meta)) {
                $entry['timestamp'] = $meta;
            }
            if (stripos($meta, 'analyzer') !== false || stripos($meta, 'system') !== false) {
                $entry['analyzer'] = $meta;
            }
            if (stripos($meta, 'operator') !== false || stripos($meta, 'validated') !== false) {
                $entry['operator'] = $meta;
            }
            if (preg_match('/[A-Z]{2,4},?[A-Z0-9]*$/', $meta)) {
                $entry['flags'] = $meta;
            }
        }

        return $entry;
    }

    private function groupSamples($samples)
    {
        $grouped = [];
        foreach ($samples as $entry) {
            $key = $entry['tube_id'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = $entry;
            } else {
                foreach (['HIV', 'HBV', 'HCV'] as $marker) {
                    if (empty($grouped[$key][$marker]) && !empty($entry[$marker])) {
                        $grouped[$key][$marker] = $entry[$marker];
                    }
                }

                foreach (['timestamp', 'operator', 'analyzer', 'flags'] as $meta) {
                    if (empty($grouped[$key][$meta]) && !empty($entry[$meta])) {
                        $grouped[$key][$meta] = $entry[$meta];
                    }
                }

                $grouped[$key]['result'] = $this->interpretResult($grouped[$key]);
                $grouped[$key]['source'] .= "\n" . $entry['source'];
            }
        }
        return $grouped;
    }

    private function interpretResult($entry)
    {
        $all = strtoupper("{$entry['HIV']} {$entry['HBV']} {$entry['HCV']}");
        if (strpos($all, 'REACTIVE') !== false) return 'Reactive';
        if (strpos($all, 'INVALID') !== false) return 'Invalid';
        return 'Non-Reactive';
    }

    private function extractMetadata($row, &$metadata)
    {
        if (stripos($row, 'analyzer') !== false) $metadata['Analyzer'] = $row;
        if (stripos($row, 'test') !== false) $metadata['Test'] = $row;
        if (stripos($row, 'flag') !== false) $metadata['Flags'] = $row;
        if (stripos($row, 'operator') !== false) $metadata['Test operator'] = $row;
        if (stripos($row, 'validated by') !== false || stripos($row, 'validated') !== false) $metadata['Validated by'] = $row;
        if (stripos($row, 'validated on') !== false) $metadata['Validated on'] = $row;
        if (stripos($row, 'control') !== false) $metadata['Negative control (batch) ID'] = $row;
    }
} 