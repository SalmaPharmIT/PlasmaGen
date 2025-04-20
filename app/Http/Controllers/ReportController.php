<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\BloodTestReport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function upload()
    {
        return view('factory.report.upload');
    }

    private function extractAsciiStrings($data, $minLen = 4) {
        preg_match_all("/[\x20-\x7E]{" . $minLen . ",}/", $data, $matches);
        return isset($matches[0]) ? $matches[0] : [];
    }

    private function extractFloatValues($data, $offset = 0, $max = 50) {
        $floats = [];
        $chunk = $data;
        for ($i = 0; $i < strlen($chunk) - 4; $i += 1) {
            $val = @unpack("f", substr($chunk, $i, 4))[1];
            if ($val !== false && is_numeric($val) && $val >= 0.001 && $val < 10.0) {
                $floats[] = round($val, 3);
                if (count($floats) >= $max) break;
            }
        }
        return $floats;
    }

    private function classifyReading($testType, $value) {
        if ($value === 0.0) return 'blank';
        switch (strtoupper($testType)) {
            case 'HCV': return $this->classifyByCutoff($value, 0.2, 0.3);
            case 'HBV': return $this->classifyByCutoff($value, 0.25, 0.35);
            case 'HIV': return $this->classifyByCutoff($value, 0.15, 0.25);
            case 'DAILY':
            default:
                return $value > 1.0 ? 'reactive' : ($value > 0.1 ? 'nonreactive' : 'blank');
        }
    }

    private function classifyByCutoff($value, $low, $high) {
        if ($value < $low) return 'nonreactive';
        if ($value < $high) return 'borderline';
        return 'reactive';
    }

    private function getCutoffs($testType) {
        switch (strtoupper($testType)) {
            case 'HCV': return ['low' => 0.2, 'high' => 0.3];
            case 'HBV': return ['low' => 0.25, 'high' => 0.35];
            case 'HIV': return ['low' => 0.15, 'high' => 0.25];
            case 'DAILY': return ['low' => 0.1, 'high' => 1.0];
            default: return ['low' => 0.1, 'high' => 1.0];
        }
    }

    private function parseTimestampFromFilename($filename) {
        if (preg_match("/_(\d{12,14})/", $filename, $matches)) {
            $str = $matches[1];
            $dt = \DateTime::createFromFormat('dmyHis', $str);
            if (!$dt) $dt = \DateTime::createFromFormat('dmyHi', substr($str, 0, 10));
            return $dt ? $dt->format('d-M-Y H:i:s') : 'Unknown';
        }
        return 'Unknown';
    }

    private function findPlateOrSerial($asciiList) {
        foreach ($asciiList as $line) {
            if (stripos($line, 'plate') !== false || stripos($line, 'SN:') !== false) {
                return $line;
            }
        }
        return 'N/A';
    }

    public function store(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('File upload request:', [
                'has_files' => $request->hasFile('report_files'),
                'files_count' => $request->hasFile('report_files') ? count($request->file('report_files')) : 0,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name
            ]);

            $request->validate([
                'report_files' => 'required',
                'report_files.*' => [
                    'file',
                    function ($attribute, $value, $fail) {
                        $allowedTypes = ['res', 'xlsx', 'xls'];
                        $extension = $value->getClientOriginalExtension();
                        
                        if (!in_array(strtolower($extension), $allowedTypes)) {
                            $fail('The report file must be a file of type: ' . implode(', ', $allowedTypes) . '.');
                        }
                    },
                    'max:10240' // 10MB max per file
                ]
            ]);

            $files = $request->file('report_files');
            $results = [];

            foreach ($files as $file) {
                if (!$file->isValid()) {
                    continue;
                }

                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                
                // Generate a unique filename
                $uniqueFilename = time() . '_' . uniqid() . '_' . $originalName;
                
                // Store the file
                $path = $file->storeAs('reports', $uniqueFilename, 'public');
                
                if ($extension === 'res') {
                    // Process RES file
                    $bin = file_get_contents($file->getRealPath());
                    $ascii = $this->extractAsciiStrings($bin);
                    
                    // Extract timestamps and sequence IDs
                    $embeddedTimestamps = [];
                    $sequence_ids = [];
                    foreach ($ascii as $line) {
                        if (preg_match_all('/\b20\d{12}\b/', $line, $matches)) {
                            foreach ($matches[0] as $rawTs) {
                                $dt = \DateTime::createFromFormat('YmdHis', $rawTs);
                                if ($dt) $embeddedTimestamps[] = $dt->format('d-M-Y H:i:s');
                            }
                        }
                        if (preg_match_all('/\b\d{12}\b/', $line, $matches)) {
                            foreach ($matches[0] as $id) {
                                $sequence_ids[] = $id;
                            }
                        }
                    }
                    $embeddedTimestamps = array_values(array_unique($embeddedTimestamps));
                    $sequence_ids = array_values(array_unique($sequence_ids));

                    $sampleId = pathinfo($originalName, PATHINFO_FILENAME);
                    $timestamp = $this->parseTimestampFromFilename($originalName);
                    $plateInfo = $this->findPlateOrSerial($ascii);

                    $operator = $instrument = $protocol = '';
                    foreach ($ascii as $line) {
                        if (strpos($line, "_robin") !== false) $operator = $line;
                        if (stripos($line, "BIO-RAD") !== false) $instrument = $line;
                        if (strpos($line, "WasherClean") !== false) $protocol = $line;
                    }

                    $floatValues = $this->extractFloatValues($bin);
                    $testType = strtoupper(explode('_', $originalName)[0]);
                    $cutoffs = $this->getCutoffs($testType);

                    // Create CSV file
                    $csvPath = 'reports/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.csv';
                    $csvFile = fopen(storage_path('app/public/' . $csvPath), 'w');
                    
                    // Write headers
                    fputcsv($csvFile, ['Timestamp', 'Well Label', 'MiniPool ID', 'OD Value', 'Result']);
                    
                    // Prepare readings data
                    $readings = [];
                    $summary = [
                        'nonreactive' => 0,
                        'borderline' => 0,
                        'reactive' => 0
                    ];

                    // Write data and prepare readings array
                    foreach ($floatValues as $index => $v) {
                        $readingTimestamp = $embeddedTimestamps[$index] ?? $timestamp;
                        $category = $this->classifyReading($testType, $v);
                        $well = "W" . ($index + 1);
                        $seqId = $sequence_ids[$index] ?? "NA";
                        
                        // Write to CSV
                        fputcsv($csvFile, [
                            $readingTimestamp,
                            $well,
                            $seqId,
                            $v,
                            ucfirst($category)
                        ]);

                        // Store in database
                        $report = BloodTestReport::create([
                            'minipool_id' => $seqId,
                            'well_number' => $well,
                            'od_value' => $v,
                            'test_timestamp' => $readingTimestamp,
                            'hbv_result' => $testType === 'HBV' ? $category : null,
                            'hcv_result' => $testType === 'HCV' ? $category : null,
                            'hiv_result' => $testType === 'HIV' ? $category : null,
                            'final_result' => $category,
                            'file_name' => $originalName,
                            'operator' => $operator,
                            'instrument' => $instrument,
                            'protocol' => $protocol,
                            'test_type' => $testType,
                            'file_path' => $path,
                            'summary' => $summary,
                            'created_by' => Auth::id()
                        ]);

                        // Log the created report with user tracking
                        \Log::info('Report created:', [
                            'report_id' => $report->id,
                            'minipool_id' => $seqId,
                            'created_by' => $report->created_by,
                            'created_at' => $report->created_at
                        ]);

                        // Add to readings array
                        $readings[] = [
                            'timestamp' => $readingTimestamp,
                            'well_label' => $well,
                            'sequence_id' => $seqId,
                            'value' => $v,
                            'category' => $category
                        ];

                        // Update summary counts
                        $summary[$category]++;
                    }
                    
                    fclose($csvFile);

                    $results[] = [
                        'file_name' => pathinfo($originalName, PATHINFO_FILENAME) . '.csv',
                        'file_path' => $csvPath,
                        'readings' => $readings,
                        'summary' => $summary,
                        'test_type' => $testType,
                        'plate_info' => $plateInfo,
                        'operator' => $operator,
                        'instrument' => $instrument,
                        'protocol' => $protocol
                    ];
                } else {
                    // Handle regular Excel file
                    $results[] = [
                        'file_name' => $originalName,
                        'file_path' => $path
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($results) . ' file(s) processed successfully.',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Error processing report: ' . $e->getMessage());
            throw $e;
        }
    }

    public function save(Request $request)
    {
        try {
            $readings = $request->input('readings', []);
            
            if (empty($readings)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data to save'
                ]);
            }

            $savedCount = 0;
            $skippedCount = 0;
            $duplicateCount = 0;

            // Log the save operation start
            \Log::info('Starting save operation:', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'readings_count' => count($readings)
            ]);

            foreach ($readings as $reading) {
                // Check if minipool_id already exists
                $existingRecord = BloodTestReport::where('minipool_id', $reading['sequence_id'])->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update([
                        'od_value' => $reading['value'],
                        'test_timestamp' => $reading['timestamp'],
                        'hbv_result' => $reading['hbv'] ?? null,
                        'hcv_result' => $reading['hcv'] ?? null,
                        'hiv_result' => $reading['hiv'] ?? null,
                        'final_result' => $reading['final_result'] ?? 'nonreactive',
                        'updated_by' => Auth::id()
                    ]);

                    // Log the update
                    \Log::info('Report updated:', [
                        'report_id' => $existingRecord->id,
                        'minipool_id' => $reading['sequence_id'],
                        'updated_by' => Auth::id(),
                        'updated_at' => now()
                    ]);

                    $duplicateCount++;
                    continue;
                }

                // Create new record
                $report = BloodTestReport::create([
                    'minipool_id' => $reading['sequence_id'],
                    'well_number' => $reading['well_label'],
                    'od_value' => $reading['value'],
                    'test_timestamp' => $reading['timestamp'],
                    'hbv_result' => $reading['hbv'] ?? null,
                    'hcv_result' => $reading['hcv'] ?? null,
                    'hiv_result' => $reading['hiv'] ?? null,
                    'final_result' => $reading['final_result'] ?? 'nonreactive',
                    'file_name' => 'Final Results Summary',
                    'operator' => Auth::user()->name,
                    'instrument' => 'Final Results',
                    'protocol' => 'Final Results Protocol',
                    'test_type' => 'FINAL',
                    'file_path' => 'final_results',
                    'summary' => [
                        'nonreactive' => 0,
                        'borderline' => 0,
                        'reactive' => 0
                    ],
                    'created_by' => Auth::id()
                ]);

                // Log the creation
                \Log::info('New report created:', [
                    'report_id' => $report->id,
                    'minipool_id' => $reading['sequence_id'],
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ]);

                $savedCount++;
            }

            // Log the save operation completion
            \Log::info('Save operation completed:', [
                'saved_count' => $savedCount,
                'duplicate_count' => $duplicateCount,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => sprintf(
                    'Results processed: %d new records saved, %d duplicates skipped',
                    $savedCount,
                    $duplicateCount
                )
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving final results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save results: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkExisting(Request $request)
    {
        try {
            $readings = $request->input('readings', []);
            $exists = false;

            foreach ($readings as $reading) {
                if (BloodTestReport::where('minipool_id', $reading['sequence_id'])->exists()) {
                    $exists = true;
                    break;
                }
            }

            return response()->json([
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking existing files: ' . $e->getMessage());
            return response()->json([
                'exists' => false
            ]);
        }
    }
} 