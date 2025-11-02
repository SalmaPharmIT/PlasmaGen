<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Models\NATTestReport;
use App\Models\NATReTestReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class NATReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('factory.report.NATUpload');
    }

    public function retestMegaPoolIndex()
    {
        return view('factory.newbagentry.nat_re_test_mega_pool_entry');
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
            // Get uploaded files
            $file1 = $request->file('file1');
            $file2 = $request->file('file2');

            $filename1 = $file1->getClientOriginalName();
            $filename2 = $file2->getClientOriginalName();

            // Create date-based subfolder and save with original filenames
            $dateFolder = date('Y-m-d');
            $timestamp = date('His'); // HourMinuteSecond for uniqueness
            $filename1WithTime = $timestamp . '_' . $filename1;
            $filename2WithTime = $timestamp . '_' . $filename2;

            $savedPath1 = $file1->storeAs('nat_reports/' . $dateFolder, $filename1WithTime, 'public');
            $savedPath2 = $file2->storeAs('nat_reports/' . $dateFolder, $filename2WithTime, 'public');

            Log::info('NAT Report files saved', [
                'file1' => $savedPath1,
                'file2' => $savedPath2,
                'user_id' => Auth::id()
            ]);

            // Load spreadsheets for processing
            $spreadsheet1 = IOFactory::load($file1->getPathname());
            $spreadsheet2 = IOFactory::load($file2->getPathname());

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

            return view('factory.report.NATUpload', [
                'metadataBlocks' => $metadataBlocks,
                'groupedSamples' => $groupedSamples,
                'savedFiles' => [
                    'file1' => $savedPath1,
                    'file2' => $savedPath2
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing NAT report files: ' . $e->getMessage());
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

                    // Debug: Log surrounding data structure (first 3 samples only)
                    if (config('app.debug') && count($samples) < 3) {
                        Log::debug("Raw data around tube ID: {$cell} at row {$i}", [
                            'current_row' => $data[$i] ?? [],
                            'next_1' => $data[$i + 1] ?? [],
                            'next_2' => $data[$i + 2] ?? [],
                            'next_3' => $data[$i + 3] ?? [],
                            'next_4' => $data[$i + 4] ?? [],
                            'next_5' => $data[$i + 5] ?? [],
                        ]);
                    }

                    $entry = $this->extractMarkers($entry, $data, $i);
                    $entry = $this->extractMetadataFromSurrounding($entry, $data, $i);
                    $entry['result'] = $this->interpretResult($entry);

                    // Debug logging (can be removed after verification)
                    if (config('app.debug')) {
                        Log::debug("Extracted sample: {$entry['tube_id']}", [
                            'HIV' => $entry['HIV'] ?: 'EMPTY',
                            'HBV' => $entry['HBV'] ?: 'EMPTY',
                            'HCV' => $entry['HCV'] ?: 'EMPTY',
                            'result' => $entry['result'],
                            'timestamp' => $entry['timestamp'] ?: 'EMPTY',
                            'analyzer' => $entry['analyzer'] ?: 'EMPTY',
                            'operator' => $entry['operator'] ?: 'EMPTY'
                        ]);
                    }

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

        // Trim whitespace and remove trailing periods
        $cell = trim($cell);
        $cell = rtrim($cell, '.');

        // Pattern 1: Direct MG format (e.g., MG2509062199)
        if (preg_match('/^MG\d{10}$/', $cell)) {
            return true;
        }

        // Pattern 2: Prefix with underscore and MG (e.g., R1921_MG2509012210)
        if (preg_match('/^[A-Z0-9]+_MG\d{10}$/', $cell)) {
            return true;
        }

        // Pattern 3: Numeric with underscores (e.g., 1921_250901220701)
        if (preg_match('/^\d+_\d{12,}$/', $cell)) {
            return true;
        }

        // Pattern 4: Legacy slash format (e.g., AB/CD/12/3456)
        if (preg_match('/^[A-Z]{2}\/[A-Z]{2}\/\d{2}\/\d{4}$/', $cell)) {
            return true;
        }

        // Pattern 5: Any alphanumeric starting with letter/number containing underscore and MG
        if (preg_match('/^[A-Z0-9]+_[A-Z0-9]+$/', $cell) && strlen($cell) >= 10) {
            return true;
        }

        return false;
    }

    private function createSampleEntry($tubeId, $sourceLabel)
    {
        // Normalize tube ID by removing trailing periods
        $tubeId = trim($tubeId);
        $tubeId = rtrim($tubeId, '.');

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
        // Search in current row and next 15 rows (vertical search)
        for ($j = 0; $j <= 15; $j++) {
            $searchRow = $data[$currentRow + $j] ?? null;
            if (!is_array($searchRow)) continue;

            // Also search the entire row content as a single string
            $rowString = implode(' ', array_map(function($cell) {
                return is_string($cell) ? trim($cell) : '';
            }, $searchRow));

            // Check individual cells and their neighbors (for separate column format)
            $rowArray = array_values($searchRow);
            for ($cellIdx = 0; $cellIdx < count($rowArray); $cellIdx++) {
                $val = $rowArray[$cellIdx];
                if (!is_string($val)) continue;
                $val = trim($val);
                if (empty($val)) continue;

                $valUpper = strtoupper($val);

                // Format 1: WITH colon (e.g., "HIV:N.R.", "HIV: Reactive")
                if (preg_match('/HIV\s*:\s*(.+)/i', $val, $matches)) {
                    if (empty($entry['HIV'])) {
                        $result = trim($matches[1]);
                        $result = preg_replace('/\s*(reactive|nonreactive|n\.r\.|invalid)/i', '$1', $result);
                        $entry['HIV'] = $result;
                    }
                }

                if (preg_match('/HBV\s*:\s*(.+)/i', $val, $matches)) {
                    if (empty($entry['HBV'])) {
                        $result = trim($matches[1]);
                        $result = preg_replace('/\s*(reactive|nonreactive|n\.r\.|invalid)/i', '$1', $result);
                        $entry['HBV'] = $result;
                    }
                }

                if (preg_match('/HCV\s*:\s*(.+)/i', $val, $matches)) {
                    if (empty($entry['HCV'])) {
                        $result = trim($matches[1]);
                        $result = preg_replace('/\s*(reactive|nonreactive|n\.r\.|invalid)/i', '$1', $result);
                        $entry['HCV'] = $result;
                    }
                }

                // Format 2: WITHOUT colon (separate columns - e.g., "HIV" in one cell, "Invalid" in next)
                // Check if this cell is a marker name, and next cell is the value
                if ($cellIdx + 1 < count($rowArray)) {
                    $nextVal = is_string($rowArray[$cellIdx + 1]) ? trim($rowArray[$cellIdx + 1]) : '';

                    if ($valUpper === 'HIV' && !empty($nextVal) && empty($entry['HIV'])) {
                        // Next cell should be the result (Invalid, Reactive, etc.)
                        if (preg_match('/^(invalid|reactive|nonreactive|n\.r\.)$/i', $nextVal)) {
                            $entry['HIV'] = $nextVal;
                        }
                    }

                    if ($valUpper === 'HBV' && !empty($nextVal) && empty($entry['HBV'])) {
                        if (preg_match('/^(invalid|reactive|nonreactive|n\.r\.)$/i', $nextVal)) {
                            $entry['HBV'] = $nextVal;
                        }
                    }

                    if ($valUpper === 'HCV' && !empty($nextVal) && empty($entry['HCV'])) {
                        if (preg_match('/^(invalid|reactive|nonreactive|n\.r\.)$/i', $nextVal)) {
                            $entry['HCV'] = $nextVal;
                        }
                    }
                }
            }

            // Also try to extract from the combined row string (with colon)
            if (empty($entry['HIV']) && preg_match('/HIV\s*:\s*([^\s]+)/i', $rowString, $matches)) {
                $entry['HIV'] = trim($matches[1]);
            }
            if (empty($entry['HBV']) && preg_match('/HBV\s*:\s*([^\s]+)/i', $rowString, $matches)) {
                $entry['HBV'] = trim($matches[1]);
            }
            if (empty($entry['HCV']) && preg_match('/HCV\s*:\s*([^\s]+)/i', $rowString, $matches)) {
                $entry['HCV'] = trim($matches[1]);
            }

            // Stop searching if all three markers are found
            if (!empty($entry['HIV']) && !empty($entry['HBV']) && !empty($entry['HCV'])) {
                break;
            }
        }

        // If still empty, set default values
        if (empty($entry['HIV'])) $entry['HIV'] = '';
        if (empty($entry['HBV'])) $entry['HBV'] = '';
        if (empty($entry['HCV'])) $entry['HCV'] = '';

        return $entry;
    }

    private function extractMetadataFromSurrounding($entry, $data, $currentRow)
    {
        // Search in a wider range: 5 rows before and 10 rows after
        $surrounding = [];
        for ($i = -5; $i <= 10; $i++) {
            if (isset($data[$currentRow + $i])) {
                $surrounding = array_merge($surrounding, $data[$currentRow + $i]);
            }
        }

        foreach ($surrounding as $meta) {
            if (!is_string($meta)) continue;
            $meta = trim($meta);

            // Extract timestamp - multiple formats
            if (empty($entry['timestamp'])) {
                // Format: 9/11/2025 3:48 AM or 9/11/2025 10:42 AM
                if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{2}\s*(?:AM|PM)/i', $meta)) {
                    $entry['timestamp'] = $meta;
                } elseif (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}/', $meta)) {
                    $entry['timestamp'] = $meta;
                }
            }

            // Extract analyzer
            if (empty($entry['analyzer'])) {
                if (stripos($meta, 'X800') !== false || stripos($meta, 'SYSTEM') !== false) {
                    $entry['analyzer'] = $meta;
                }
            }

            // Extract operator/validator
            if (empty($entry['operator'])) {
                if (preg_match('/\d+_[a-z]+/i', $meta)) {
                    // Pattern like "815_karthik"
                    $entry['operator'] = $meta;
                } elseif (stripos($meta, 'operator') !== false) {
                    $entry['operator'] = $meta;
                }
            }

            // Extract flags
            if (empty($entry['flags']) && preg_match('/^[A-Z]{2,4}[,\s]*[A-Z0-9]*$/i', $meta) && strlen($meta) <= 10) {
                $entry['flags'] = $meta;
            }
        }

        return $entry;
    }

    private function groupSamples($samples)
    {
        $grouped = [];

        if (config('app.debug')) {
            Log::debug("Starting groupSamples with " . count($samples) . " samples");
        }

        foreach ($samples as $entry) {
            $key = $entry['tube_id'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = $entry;

                if (config('app.debug') && count($grouped) <= 3) {
                    Log::debug("First occurrence of tube ID: {$key}", [
                        'HIV' => $entry['HIV'] ?: 'EMPTY',
                        'HBV' => $entry['HBV'] ?: 'EMPTY',
                        'HCV' => $entry['HCV'] ?: 'EMPTY'
                    ]);
                }
            } else {
                // Merge data from duplicate entries
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

        if (config('app.debug')) {
            Log::debug("Finished grouping. Total unique samples: " . count($grouped));
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

    public function saveReports(Request $request)
    {


        try {
            Log::info('Starting saveReports method');
            Log::info('Received data count: ' . count($request->input('reports', [])));

            $reports = $request->input('reports', []);
            $savedCount = 0;
            $updatedCount = 0;

            foreach ($reports as $report) {
                try {
                    // Convert N.R. to nonreactive and handle null values
                    $hiv = isset($report['HIV']) ? (strtolower(trim($report['HIV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HIV']))) : null;
                    $hbv = isset($report['HBV']) ? (strtolower(trim($report['HBV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HBV']))) : null;
                    $hcv = isset($report['HCV']) ? (strtolower(trim($report['HCV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HCV']))) : null;
                    $status = isset($report['result']) ? (strtolower(trim($report['result'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['result']))) : 'nonreactive';

                    // Create array of data
                    $data = [
                        'mini_pool_id' => $report['tube_id'],
                        'hiv' => $hiv,
                        'hbv' => $hbv,
                        'hcv' => $hcv,
                        'status' => $status,
                        'result_time' => $report['timestamp'] ?? null,
                        'analyzer' => $report['analyzer'] ?? null,
                        'operator' => $report['operator'] ?? null,
                        'flags' => $report['flags'] ?? null,
                        'timestamp' => now(),
                        'created_by' => Auth::id()
                    ];

                    Log::info('Processing record:', ['mini_pool_id' => $data['mini_pool_id']]);

                    // Check if record exists
                    $existingRecord = \App\Models\NATTestReport::where('mini_pool_id', $data['mini_pool_id'])->first();

                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->update($data);
                        $updatedCount++;
                        Log::info('Updated existing record:', ['mini_pool_id' => $data['mini_pool_id']]);
                    } else {
                        // Create new record
                        $record = new \App\Models\NATTestReport($data);
                        $record->save();
                        $savedCount++;
                        Log::info('Created new record:', ['mini_pool_id' => $data['mini_pool_id']]);
                    }
                } catch (Exception $e) {
                    Log::error('Error processing individual record: ' . $e->getMessage());
                    Log::error('Record data: ', $report);
                    continue;
                }
            }

            Log::info('Operation completed', [
                'new_records' => $savedCount,
                'updated_records' => $updatedCount
            ]);


            return response()->json([
                'success' => true,
                'message' => "Successfully processed NAT test reports (New: $savedCount, Updated: $updatedCount)",
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount
            ]);

        } catch (Exception $e) {
            Log::error('Error in saveReports: ' . $e->getMessage());
            Log::error($e->getTraceAsString());



            return response()->json([
                'success' => false,
                'message' => 'Error saving NAT test reports: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateRetestReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file1' => 'required|file|mimes:xlsx,xls',
            'file2' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Get uploaded files
            $file1 = $request->file('file1');
            $file2 = $request->file('file2');

            $filename1 = $file1->getClientOriginalName();
            $filename2 = $file2->getClientOriginalName();

            // Create date-based subfolder and save with original filenames
            $dateFolder = date('Y-m-d');
            $timestamp = date('His'); // HourMinuteSecond for uniqueness
            $filename1WithTime = $timestamp . '_' . $filename1;
            $filename2WithTime = $timestamp . '_' . $filename2;

            $savedPath1 = $file1->storeAs('nat_retest_reports/' . $dateFolder, $filename1WithTime, 'public');
            $savedPath2 = $file2->storeAs('nat_retest_reports/' . $dateFolder, $filename2WithTime, 'public');

            Log::info('NAT Retest Report files saved', [
                'file1' => $savedPath1,
                'file2' => $savedPath2,
                'user_id' => Auth::id()
            ]);

            // Load spreadsheets for processing
            $spreadsheet1 = IOFactory::load($file1->getPathname());
            $spreadsheet2 = IOFactory::load($file2->getPathname());

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

            return view('factory.newbagentry.nat_re_test_mega_pool_entry', [
                'metadataBlocks' => $metadataBlocks,
                'groupedSamples' => $groupedSamples,
                'savedFiles' => [
                    'file1' => $savedPath1,
                    'file2' => $savedPath2
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing NAT retest report files: ' . $e->getMessage());
            return back()->with('error', 'Error processing files: ' . $e->getMessage());
        }
    }

    public function saveRetestReports(Request $request)
    {
        try {
            Log::info('Starting saveRetestReports method');
            Log::info('Received data count: ' . count($request->input('reports', [])));

            $reports = $request->input('reports', []);
            $savedCount = 0;
            $updatedCount = 0;

            foreach ($reports as $report) {
                try {
                    // Convert N.R. to nonreactive and handle null values
                    $hiv = isset($report['HIV']) ? (strtolower(trim($report['HIV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HIV']))) : null;
                    $hbv = isset($report['HBV']) ? (strtolower(trim($report['HBV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HBV']))) : null;
                    $hcv = isset($report['HCV']) ? (strtolower(trim($report['HCV'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['HCV']))) : null;
                    $status = isset($report['result']) ? (strtolower(trim($report['result'])) === 'n.r.' ? 'nonreactive' : strtolower(trim($report['result']))) : 'nonreactive';

                    // Create array of data
                    $data = [
                        'mini_pool_id' => $report['tube_id'],
                        'hiv' => $hiv,
                        'hbv' => $hbv,
                        'hcv' => $hcv,
                        'status' => $status,
                        'result_time' => $report['timestamp'] ?? null,
                        'analyzer' => $report['analyzer'] ?? null,
                        'operator' => $report['operator'] ?? null,
                        'flags' => $report['flags'] ?? null,
                        'timestamp' => now(),
                        'created_by' => Auth::id(),
                        'is_retest' => true  // Mark as retest
                    ];

                    Log::info('Processing retest record:', ['mini_pool_id' => $data['mini_pool_id']]);

                    // Check if record exists in the retest table
                    $existingRecord = NATReTestReport::where('mini_pool_id', $data['mini_pool_id'])->first();

                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->update($data);
                        $updatedCount++;
                        Log::info('Updated existing retest record:', ['mini_pool_id' => $data['mini_pool_id']]);
                    } else {
                        // Create new record in retest table
                        $record = new NATReTestReport($data);
                        $record->save();
                        $savedCount++;
                        Log::info('Created new retest record:', ['mini_pool_id' => $data['mini_pool_id']]);
                    }
                } catch (Exception $e) {
                    Log::error('Error processing individual retest record: ' . $e->getMessage());
                    Log::error('Record data: ', $report);
                    continue;
                }
            }

            Log::info('Retest operation completed', [
                'new_records' => $savedCount,
                'updated_records' => $updatedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully processed NAT retest reports (New: $savedCount, Updated: $updatedCount)",
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount
            ]);

        } catch (Exception $e) {
            Log::error('Error in saveRetestReports: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error saving NAT retest reports: ' . $e->getMessage()
            ], 500);
        }
    }
}

