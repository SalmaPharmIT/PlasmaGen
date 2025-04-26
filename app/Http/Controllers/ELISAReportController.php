<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ElisaTestReport;
use Carbon\Carbon;

class ELISAReportController extends Controller
{
    public function upload()
    {
        return view('factory.report.ELISAUpload');
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
                'test_type' => $request->input('test_type')
            ]);

            $request->validate([
                'report_files' => 'required',
                'test_type' => 'required|in:HBV,HCV,HIV',
                'report_files.*' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if (!$value->isValid()) {
                            $fail('The file upload was not successful.');
                            return;
                        }
                        
                        $extension = strtolower($value->getClientOriginalExtension());
                        if ($extension !== 'res') {
                            $fail('The file must be a RES file.');
                            return;
                        }

                        if ($value->getSize() > 10240 * 1024) { // 10MB
                            $fail('The file size must not exceed 10MB.');
                            return;
                        }
                    },
                ]
            ]);

            $files = $request->file('report_files');
            $results = [];
            $testType = $request->input('test_type');

            foreach ($files as $file) {
                try {
                    if (!$file->isValid()) {
                        \Log::error('Invalid file upload:', ['filename' => $file->getClientOriginalName()]);
                        continue;
                    }

                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    
                    // Generate a unique filename
                    $uniqueFilename = time() . '_' . uniqid() . '_' . $originalName;
                    
                    // Store the file
                    $path = $file->storeAs('reports', $uniqueFilename, 'public');
                    
                    if ($extension === 'res') {
                        try {
                            // Process RES file
                            $bin = file_get_contents($file->getRealPath());
                            if ($bin === false) {
                                throw new \Exception("Could not read file contents");
                            }
                            
                            $ascii = $this->extractAsciiStrings($bin);
                            if (empty($ascii)) {
                                throw new \Exception("No valid data found in file");
                            }
                            
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
                            
                            // Process file data and create results
                            $sampleId = pathinfo($originalName, PATHINFO_FILENAME);
                            $timestamp = $this->parseTimestampFromFilename($originalName);
                            $plateInfo = $this->findPlateOrSerial($ascii);
                            
                            // Extract float values
                            $floatValues = $this->extractFloatValues($bin);
                            if (empty($floatValues)) {
                                throw new \Exception("No valid readings found in file");
                            }

                            $readings = [];
                            $summary = ['nonreactive' => 0, 'borderline' => 0, 'reactive' => 0];

                            // Create CSV file for readings
                            $csvPath = storage_path('app/public/reports/') . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.csv';
                            $csvFile = fopen($csvPath, 'w');
                            
                            if ($csvFile === false) {
                                throw new \Exception("Could not create CSV file");
                            }

                            // Write CSV header
                            fputcsv($csvFile, ['Timestamp', 'Well', 'Sequence ID', 'Value', 'Result']);

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

                                // Update summary counts
                                if ($category !== 'blank') {
                                    $summary[$category]++;
                                }

                                // Add to readings array
                                $readings[] = [
                                    'timestamp' => $readingTimestamp,
                                    'well_label' => $well,
                                    'sequence_id' => $seqId,
                                    'value' => $v,
                                    'category' => $category
                                ];
                            }
                            
                            fclose($csvFile);

                            $results[] = [
                                'file_name' => $originalName,
                                'file_path' => Storage::url('reports/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.csv'),
                                'readings' => $readings,
                                'summary' => $summary,
                                'test_type' => $testType,
                                'plate_info' => $plateInfo,
                                'operator' => 'System',
                                'instrument' => 'Auto',
                                'protocol' => 'Standard'
                            ];

                        } catch (\Exception $e) {
                            \Log::error('Error processing RES file:', [
                                'filename' => $originalName,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            
                            // Delete the stored file if processing failed
                            Storage::disk('public')->delete($path);
                            
                            return response()->json([
                                'success' => false,
                                'message' => 'Error processing file ' . $originalName . ': ' . $e->getMessage()
                            ], 422);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing file:', [
                        'filename' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Error processing file ' . $file->getClientOriginalName() . ': ' . $e->getMessage()
                    ], 422);
                }
            }

            if (empty($results)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid results were processed from the uploaded files.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => count($results) . ' file(s) processed successfully.',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in store method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the upload: ' . $e->getMessage()
            ], 500);
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

            $userId = Auth::id();
            $successCount = 0;
            $updateCount = 0;

            foreach ($readings as $reading) {
                // Determine final result
                $finalResult = 'nonreactive';
                if ($reading['hbv'] === 'reactive' || $reading['hcv'] === 'reactive' || $reading['hiv'] === 'reactive') {
                    $finalResult = 'reactive';
                } elseif ($reading['hbv'] === 'borderline' || $reading['hcv'] === 'borderline' || $reading['hiv'] === 'borderline') {
                    $finalResult = 'borderline';
                }

                $data = [
                    'mini_pool_id' => $reading['sequence_id'],
                    'well_num' => $reading['well_label'],
                    'od_value' => $reading['value'],
                    'result_time' => $reading['timestamp'],
                    'hbv' => $reading['hbv'] ?? null,
                    'hcv' => $reading['hcv'] ?? null,
                    'hiv' => $reading['hiv'] ?? null,
                    'final_result' => $finalResult,
                    'timestamp' => Carbon::parse($reading['timestamp']),
                    'updated_by' => $userId
                ];

                // Check if record exists
                $existingRecord = ElisaTestReport::where('mini_pool_id', $reading['sequence_id'])
                    ->where('well_num', $reading['well_label'])
                    ->first();

                if ($existingRecord) {
                    // Update existing record
                    $existingRecord->update($data);
                    $updateCount++;
                } else {
                    // Create new record
                    $data['created_by'] = $userId;
                    ElisaTestReport::create($data);
                    $successCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully processed results: {$successCount} new records created, {$updateCount} records updated.",
                'data' => [
                    'new_records' => $successCount,
                    'updated_records' => $updateCount
                ]
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
        // Since we're not using a database, always return false
        return response()->json([
            'exists' => false
        ]);
    }
} 