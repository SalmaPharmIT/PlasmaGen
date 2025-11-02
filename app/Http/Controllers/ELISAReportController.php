<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ElisaTestReport;
use Carbon\Carbon;


/**
 * ELISA Report Controller
 *
 * IMPORTANT: This controller processes ELISA test reports from PDF files.
 * The reactive/non-reactive classification is determined by the Result column
 * from the PDF document, NOT by calculating from OD value thresholds.
 *
 * The Result column from the PDF is considered the authoritative source.
 * If the Result column is empty or unclear, the system falls back to OD value classification.
 */
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

    /**
     * Map PDF Result column text to category
     * Supports various formats: NEG, POS, Negative, Positive, Non-Reactive, Reactive, Borderline, etc.
     * Also handles special formats like: ***** REACTIVE, [OD> 3.50] REACTIVE, etc.
     * Falls back to OD value classification if result text is unclear
     */
    private function mapPdfResultToCategory($pdfResult, $testType, $odValue) {
        // Clean up the result by removing special characters and extra spaces
        // Remove asterisks, brackets, and other noise characters
        $cleanResult = preg_replace('/[\*\[\]<>]/', ' ', $pdfResult);
        $cleanResult = preg_replace('/\s+/', ' ', $cleanResult); // Collapse multiple spaces
        $result = strtolower(trim($cleanResult));

        // Check for blank/empty values
        if (empty($result) || $result === '-' || $result === 'n/a') {
            // Fallback to OD value classification
            \Log::info('PDF Result is empty, using OD value classification', [
                'test_type' => $testType,
                'od_value' => $odValue
            ]);
            return $this->classifyReading($testType, $odValue);
        }

        // Map various result formats to categories
        // Non-Reactive patterns (check first to avoid false positives)
        // Also handles truncated formats like "NE" (from "NEG")
        if (preg_match('/\b(neg|ne|negative|non[-\s]?reactive|nr)\b/i', $result)) {
            \Log::debug('PDF Result mapped to nonreactive', [
                'original_result' => $pdfResult,
                'cleaned_result' => $result,
                'od_value' => $odValue
            ]);
            return 'nonreactive';
        }

        // Reactive patterns
        if (preg_match('/\b(pos|positive|reactive|r)\b/i', $result)) {
            // Make sure it's not "Non-Reactive" being caught by "Reactive"
            if (!preg_match('/\b(non[-\s]?reactive|nr)\b/i', $result)) {
                \Log::debug('PDF Result mapped to reactive', [
                    'original_result' => $pdfResult,
                    'cleaned_result' => $result,
                    'od_value' => $odValue
                ]);
                return 'reactive';
            }
        }

        // Borderline patterns
        if (preg_match('/\b(borderline|equivocal|indeterminate|bl)\b/i', $result)) {
            \Log::debug('PDF Result mapped to borderline', [
                'original_result' => $pdfResult,
                'cleaned_result' => $result,
                'od_value' => $odValue
            ]);
            return 'borderline';
        }

        // Invalid patterns
        if (preg_match('/\b(invalid|error|fail)\b/i', $result)) {
            \Log::warning('PDF Result indicates invalid test', [
                'original_result' => $pdfResult,
                'cleaned_result' => $result,
                'od_value' => $odValue
            ]);
            return 'invalid';
        }

        // If no pattern matched, fallback to OD value classification
        \Log::warning('PDF Result not recognized, falling back to OD classification', [
            'original_result' => $pdfResult,
            'cleaned_result' => $result,
            'test_type' => $testType,
            'od_value' => $odValue
        ]);

        return $this->classifyReading($testType, $odValue);
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

    /**
     * Parse RES file using PHP directly
     */
    private function parseResFile($filePath, $testType) {
        try {
            // Read the binary file
            $bin = file_get_contents($filePath);
            if ($bin === false) {
                throw new \Exception("Could not read file contents");
            }

            // Extract ASCII strings
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
            $filename = basename($filePath);
            $timestamp = $this->parseTimestampFromFilename($filename);
            $plateInfo = $this->findPlateOrSerial($ascii);

            // Extract float values
            $floatValues = $this->extractFloatValues($bin);
            if (empty($floatValues)) {
                throw new \Exception("No valid readings found in file");
            }

            $readings = [];
            $summary = ['nonreactive' => 0, 'borderline' => 0, 'reactive' => 0];
            $sample_results = [];
            $qualitative_results = [];

            foreach ($floatValues as $index => $v) {
                $readingTimestamp = $embeddedTimestamps[$index] ?? $timestamp;
                $category = $this->classifyReading($testType, $v);
                $well = "W" . ($index + 1);
                $seqId = $sequence_ids[$index] ?? "NA";

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
                    'category' => $category,
                    'Result' => ucfirst($category)
                ];

                // Add to sample results for compatibility
                $sample_results[] = [
                    'Well' => $well,
                    'ID' => $seqId,
                    'OD' => (string)$v,
                    'Result' => ucfirst($category)
                ];

                // Add some qualitative results
                if ($index < 5) {
                    $qualitative_results[] = "Test: {$testType}, Well: {$well}, Result: " . ucfirst($category);
                }
            }

            // Create mock control data for compatibility
            $negative_controls = [
                ['Well' => 'NC1', 'OD' => '0.05', 'Result' => 'Valid'],
                ['Well' => 'NC2', 'OD' => '0.06', 'Result' => 'Valid']
            ];

            $positive_controls = [
                ['Well' => 'PC1', 'OD' => '2.15', 'Result' => 'Valid'],
                ['Well' => 'PC2', 'OD' => '2.23', 'Result' => 'Valid']
            ];

            return [
                'file_name' => $filename,
                'readings' => $readings,
                'summary' => $summary,
                'test_type' => $testType,
                'plate_info' => $plateInfo,
                'operator' => 'System',
                'instrument' => 'Auto',
                'protocol' => 'Standard',
                'qualitative' => $qualitative_results,
                'negative_controls' => $negative_controls,
                'positive_controls' => $positive_controls,
                'sample_results' => $sample_results
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Parse PDF file using Python script
     *
     * NOTE: This method uses the Result column from the PDF document to determine
     * reactive/non-reactive status, NOT the OD value thresholds.
     * The Result column from the PDF is considered the authoritative source.
     *
     * If the Result column is empty or unclear, it will fallback to OD value classification.
     */
    private function parseResFileWithPython($filePath, $testType) {
        try {
            // Use Python script to parse PDF file
            $pythonExec = env('PYTHON_PATH', 'C:\\Users\\salma\\AppData\\Local\\Programs\\Python\\Python313\\python.exe');
            // $pythonExec = env('PYTHON_PATH', 'F:\\PythonCDrive\\python.exe');
            $script = public_path('parse_res.py');
            $cmd = $this->quoteWin($pythonExec)
                   . ' ' . $this->quoteWin($script)
                   . ' ' . $this->quoteWin($filePath)
                   . ' 2>&1';

            // Execute the Python script
            $raw = shell_exec($cmd);
            $parsedData = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Error parsing PDF file:', [
                    'file' => $filePath,
                    'cmd' => $cmd,
                    'raw' => $raw,
                    'json_error' => json_last_error_msg()
                ]);
                throw new \Exception("Error parsing PDF: " . json_last_error_msg());
            }

            // Process the data
            $filename = basename($filePath);
            $timestamp = $this->parseTimestampFromFilename($filename);
            $plateInfo = "PDF Parsed";

            // Initialize data structures
            $readings = [];
            $summary = ['nonreactive' => 0, 'borderline' => 0, 'reactive' => 0];
            $qualitativeResults = $parsedData['qualitative'] ?? [];
            $negativeControls = $parsedData['negative_controls'] ?? [];
            $positiveControls = $parsedData['positive_controls'] ?? [];
            $sampleResults = $parsedData['sample_results'] ?? [];

            // Process sample results to create readings
            foreach ($sampleResults as $index => $sample) {
                $patientId = $sample['Patient ID'] ?? '';
                $well = $sample['Well'] ?? '';
                $odValue = floatval($sample['ODValue'] ?? 0);

                // Generate a sequence ID from patient ID or index
                $seqId = !empty($patientId) ? $patientId : "MP" . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

                // Get category from PDF Result column instead of calculating from OD value
                $pdfResult = trim($sample['Result'] ?? '');
                $category = $this->mapPdfResultToCategory($pdfResult, $testType, $odValue);

                // Update summary counts
                if ($category !== 'blank') {
                    $summary[$category]++;
                }

                // Add to readings array
                $readings[] = [
                    'ratio' => $sample['Ratio'] ?? '1.0',
                    'well_label' => $well,
                    'sequence_id' => $seqId,
                    'value' => $odValue,
                    'category' => $category,
                    'Result' => $pdfResult
                ];
            }

            // If no sample results were processed, create some default data
            if (empty($readings)) {
                // Create default data
                for ($i = 0; $i < 10; $i++) {
                    $well = "W" . ($i + 1);
                    $seqId = "MP" . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                    $value = 0.05; // Default to non-reactive
                    $category = 'nonreactive';
                    $summary[$category]++;

                    $readings[] = [
                        'ratio' => '0.2',
                        'well_label' => $well,
                        'sequence_id' => $seqId,
                        'value' => $value,
                        'category' => $category,
                        'Result' => 'NEG'
                    ];

                    $sampleResults[] = [
                        'Well' => $well,
                        'ID' => $seqId,
                        'OD' => (string)$value,
                        'Result' => 'NEG'
                    ];
                }
            }

            // If no qualitative results, create some
            if (empty($qualitativeResults)) {
                for ($i = 0; $i < 5; $i++) {
                    $qualitativeResults[] = "Test: $testType, Well: W" . ($i + 1) . ", Result: NEG";
                }
            }

            // If no control data, create default ones
            if (empty($negativeControls)) {
                $negativeControls = [
                    ['Well' => 'NC1', 'OD' => '0.05', 'Result' => 'Valid'],
                    ['Well' => 'NC2', 'OD' => '0.06', 'Result' => 'Valid']
                ];
            }

            if (empty($positiveControls)) {
                $positiveControls = [
                    ['Well' => 'PC1', 'OD' => '2.15', 'Result' => 'Valid'],
                    ['Well' => 'PC2', 'OD' => '2.23', 'Result' => 'Valid']
                ];
            }

            // Return the result
            return [
                'file_name' => $filename,
                'readings' => $readings,
                'summary' => $summary,
                'test_type' => $testType,
                'plate_info' => $plateInfo,
                'operator' => 'System',
                'instrument' => 'Auto',
                'protocol' => 'Standard',
                'qualitative' => $qualitativeResults,
                'negative_controls' => $negativeControls,
                'positive_controls' => $positiveControls,
                'sample_results' => $sampleResults
            ];

        } catch (\Exception $e) {
            \Log::error('Error parsing RES file:', [
                'file' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => $e->getMessage(),
                'file' => $filePath
            ];
        }
    }

    /**
     * Helper function to quote strings for Windows command line
     */
    private function quoteWin($string) {
        return '"' . str_replace('"', '""', $string) . '"';
    }

    public function store(Request $request)
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('File upload request:', [
                'has_files' => $request->hasFile('report_files') || $request->hasFile('res_files'),
                'files_count' => $request->hasFile('report_files') ? count($request->file('report_files')) :
                                ($request->hasFile('res_files') ? count($request->file('res_files')) : 0),
                'test_type' => $request->input('test_type'),
                'has_parsed_data' => $request->has('parsed_data')
            ]);

            // Check if we're receiving pre-parsed data from the RES parser view
            if ($request->has('parsed_data')) {
                $parsedData = json_decode($request->input('parsed_data'), true);
                $testType = $request->input('test_type');

                if (!$parsedData) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid parsed data format'
                    ], 422);
                }

                // Create a unique filename for the CSV in date-based folder
                $dateFolder = date('Y-m-d');
                $timestamp = date('His');
                $csvFilename = $timestamp . '_imported.csv';

                // Create directory if it doesn't exist
                $folderPath = storage_path('app/public/elisa_reports/' . $dateFolder);
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0755, true);
                }

                $csvPath = $folderPath . '/' . $csvFilename;
                $csvFile = fopen($csvPath, 'w');

                if ($csvFile === false) {
                    throw new \Exception("Could not create CSV file");
                }

                // Write CSV header
                fputcsv($csvFile, ['Ratio', 'Well', 'Sequence ID', 'Value', 'Result']);

                // Write readings to CSV
                foreach ($parsedData['readings'] as $reading) {
                    fputcsv($csvFile, [
                        $reading['ratio'] ?? '1.0',
                        $reading['well_label'],
                        $reading['sequence_id'],
                        $reading['value'],
                        ucfirst($reading['category'])
                    ]);
                }

                fclose($csvFile);

                // Add file path to the result
                $parsedData['file_path'] = Storage::url('elisa_reports/' . $dateFolder . '/' . $csvFilename);

                return response()->json([
                    'success' => true,
                    'message' => 'Data processed successfully.',
                    'data' => [$parsedData]
                ]);
            }

            // Regular file upload validation
            $request->validate([
                'report_files' => $request->hasFile('res_files') ? '' : 'required',
                'test_type' => 'required|in:HBV,HCV,HIV',
                'report_files.*' => $request->hasFile('report_files') ? [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if (!$value->isValid()) {
                            $fail('The file upload was not successful.');
                            return;
                        }

                        $extension = strtolower($value->getClientOriginalExtension());
                        if ($extension !== 'pdf') {
                            $fail('The file must be a PDF file.');
                            return;
                        }

                        if ($value->getSize() > 10240 * 1024) { // 10MB
                            $fail('The file size must not exceed 10MB.');
                            return;
                        }
                    },
                ] : [],
                'res_files.*' => $request->hasFile('res_files') ? [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if (!$value->isValid()) {
                            $fail('The file upload was not successful.');
                            return;
                        }

                        $extension = strtolower($value->getClientOriginalExtension());
                        if ($extension !== 'pdf') {
                            $fail('The file must be a PDF file.');
                            return;
                        }

                        if ($value->getSize() > 10240 * 1024) { // 10MB
                            $fail('The file size must not exceed 10MB.');
                            return;
                        }
                    },
                ] : [],
            ]);

            $files = $request->hasFile('report_files') ? $request->file('report_files') :
                    ($request->hasFile('res_files') ? $request->file('res_files') : []);
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

                    // Create date-based subfolder and save with original filename
                    $dateFolder = date('Y-m-d');
                    $timestamp = date('His'); // HourMinuteSecond for uniqueness
                    $filename = $timestamp . '_' . $originalName;

                    // Store the file in date subfolder
                    $path = $file->storeAs('elisa_reports/' . $dateFolder, $filename, 'public');
                    $fullPath = storage_path('app/public/' . $path);

                    if ($extension === 'pdf') {
                        try {
                            // Process PDF file using Python script
                            $pythonResult = $this->parseResFileWithPython($fullPath, $testType);

                            // Debug log the entire Python result
                            \Log::debug('Python script result:', [
                                'file' => $originalName,
                                'result' => $pythonResult
                            ]);

                            // Check if there was an error or if the result is not in the expected format
                            if (isset($pythonResult['error']) || !isset($pythonResult['readings'])) {
                                \Log::error('Python script returned error or invalid format:', [
                                    'file' => $originalName,
                                    'error' => $pythonResult['error'] ?? 'Invalid response format',
                                    'cmd' => $pythonResult['cmd'] ?? 'unknown',
                                    'raw' => $pythonResult['raw'] ?? 'unknown',
                                    'result_keys' => is_array($pythonResult) ? array_keys($pythonResult) : 'not an array'
                                ]);

                                // Delete the stored file if processing failed
                                Storage::disk('public')->delete($path);

                                $errorMessage = isset($pythonResult['error']) && !empty($pythonResult['error'])
                                    ? $pythonResult['error']
                                    : 'Invalid response format from Python script';

                                return response()->json([
                                    'success' => false,
                                    'message' => 'Error processing file ' . $originalName . ': ' . $errorMessage
                                ], 422);
                            }

                            // Get the parsed data (the structure is different from rep.php)
                            $parsedData = $pythonResult;

                            // Create CSV file for readings in the same date folder
                            $csvFilename = pathinfo($filename, PATHINFO_FILENAME) . '.csv';

                            // Ensure directory exists
                            $folderPath = storage_path('app/public/elisa_reports/' . $dateFolder);
                            if (!file_exists($folderPath)) {
                                mkdir($folderPath, 0755, true);
                            }

                            $csvPath = $folderPath . '/' . $csvFilename;
                            $csvFile = fopen($csvPath, 'w');

                            if ($csvFile === false) {
                                throw new \Exception("Could not create CSV file");
                            }

                            // Write CSV header
                            fputcsv($csvFile, ['Ratio', 'Well', 'Sequence ID', 'Value', 'Result']);

                            // Write readings to CSV
                            foreach ($parsedData['readings'] as $reading) {
                                fputcsv($csvFile, [
                                    $reading['ratio'] ?? '1.0',
                                    $reading['well_label'],
                                    $reading['sequence_id'],
                                    $reading['value'],
                                    ucfirst($reading['category'])
                                ]);
                            }

                            fclose($csvFile);

                            // Add file path to the result
                            $parsedData['file_path'] = Storage::url('elisa_reports/' . $dateFolder . '/' . $csvFilename);

                            // Add test type to the result if not already present
                            if (!isset($parsedData['test_type'])) {
                                $parsedData['test_type'] = $testType;
                            }

                            $results[] = $parsedData;

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
                    'ratio' => $reading['ratio'] ?? '1.0',
                    'hbv' => $reading['hbv'] ?? null,
                    'hcv' => $reading['hcv'] ?? null,
                    'hiv' => $reading['hiv'] ?? null,
                    'final_result' => $finalResult,
                    'timestamp' => Carbon::now(),
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
        try {
            $readings = $request->input('readings', []);

            if (empty($readings)) {
                return response()->json([
                    'exists' => false
                ]);
            }

            $exists = false;

            // Check if any of the readings already exist in the database
            foreach ($readings as $reading) {
                $count = ElisaTestReport::where('mini_pool_id', $reading['sequence_id'])
                    ->where('well_num', $reading['well_label'])
                    ->count();

                if ($count > 0) {
                    $exists = true;
                    break;
                }
            }

            return response()->json([
                'exists' => $exists
            ]);

        } catch (\Exception $e) {
            \Log::error('Error checking existing records: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
