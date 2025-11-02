<?php

namespace App\Http\Controllers;

use App\Models\SubMiniPoolEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubMiniPoolEntryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_no' => 'required|string',
                'segment_number' => 'required|array',
                'donor_id' => 'required|array',
                'donation_date' => 'required|array',
                'blood_group' => 'required|array',
                'bag_volume' => 'required|array',
                'tail_cutting' => 'required|array'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Get the mini pool number
            $miniPoolNumber = $request->input('mini_pool_no');

            // Extract segment numbers (sub mini pool numbers)
            $segmentNumbers = array_filter($request->input('segment_number', []));
            $uniqueSegmentNumbers = array_unique($segmentNumbers);

            // Convert to comma-separated string
            $subMiniPoolNoString = implode(',', $uniqueSegmentNumbers);

            // Check if entry already exists
            $existingEntry = SubMiniPoolEntry::where('mini_pool_number', $miniPoolNumber)->first();

            if ($existingEntry) {
                // Update existing entry
                $existingEntry->sub_mini_pool_no = $subMiniPoolNoString;
                $existingEntry->updated_by = Auth::id();
                $existingEntry->save();

                $message = 'Sub Mini Pool Entry updated successfully';
                $subMiniPoolEntry = $existingEntry;
            } else {
                // Create new entry
                $subMiniPoolEntry = SubMiniPoolEntry::create([
                    'mini_pool_number' => $miniPoolNumber,
                    'sub_mini_pool_no' => $subMiniPoolNoString,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id()
                ]);

                $message = 'Sub Mini Pool Entry created successfully';
            }

            // Log the data being saved
            Log::info('Saving SubMiniPoolEntry', [
                'mini_pool_number' => $miniPoolNumber,
                'sub_mini_pool_no' => $subMiniPoolNoString,
                'user' => Auth::user()->name ?? 'Unknown'
            ]);

            // Create ELISA test report entries for each sub mini pool
            $this->createElisaTestEntries($uniqueSegmentNumbers);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error creating SubMiniPoolEntry: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Failed to create Sub Mini Pool Entry: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getMiniPoolNumbers(Request $request)
    {
        try {
            // Get mini pool IDs from elisa_test_report table where final_result is 'Reactive'
            $miniPoolNumbers = DB::table('elisa_test_report')
                ->select('mini_pool_id')
                ->where('final_result', 'Reactive')
                ->whereNotNull('mini_pool_id')
                ->where('mini_pool_id', '!=', '')
                ->distinct()
                ->orderBy('mini_pool_id')
                ->pluck('mini_pool_id')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $miniPoolNumbers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mini pool numbers: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch mini pool numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubMiniPoolNumbers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mini Pool Number is required'
                ], 400);
            }

            // Get sub mini pool numbers
            $subMiniPoolEntry = SubMiniPoolEntry::where('mini_pool_number', $request->mini_pool_number)
                ->first();

            if (!$subMiniPoolEntry) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No sub mini pool numbers found for this mini pool'
                ], 404);
            }

            // Get bag entries detail IDs from bag_entries_mini_pools
            $bagEntriesMiniPool = DB::table('bag_entries_mini_pools')
                ->where('mini_pool_number', $request->mini_pool_number)
                ->first();

            if (!$bagEntriesMiniPool) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No bag entries found for this mini pool'
                ], 404);
            }

            // Split the comma-separated sub mini pool numbers into an array
            $subMiniPoolNumbers = explode(',', $subMiniPoolEntry->sub_mini_pool_no);

            // Get the count of bag entries detail IDs
            $bagEntriesDetailIds = explode(',', trim($bagEntriesMiniPool->bag_entries_detail_ids, '[]'));
            $rowCount = count($bagEntriesDetailIds);

            return response()->json([
                'status' => 'success',
                'data' => $subMiniPoolNumbers,
                'row_count' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sub mini pool numbers: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sub mini pool numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create ELISA test report entries for sub mini pools
     *
     * @param array $subMiniPoolNumbers Array of sub mini pool numbers
     * @return void
     */
    private function createElisaTestEntries(array $subMiniPoolNumbers)
    {
        try {
            // Import the model
            $elisaTestReportModel = app(\App\Models\ElisaTestReport::class);

            foreach ($subMiniPoolNumbers as $subMiniPoolNumber) {
                // Check if entry already exists
                $existingEntry = $elisaTestReportModel->where('mini_pool_id', $subMiniPoolNumber)->first();

                if (!$existingEntry) {
                    // Create new entry with default values
                    $elisaTestReportModel->create([
                        'mini_pool_id' => $subMiniPoolNumber,
                        'well_num' => null,
                        'od_value' => null,
                        'result_time' => null,
                        'hbv' => null,
                        'hcv' => null,
                        'hiv' => null,
                        'final_result' => null, // Will be filled during ELISA testing
                        'timestamp' => now(),
                        'created_by' => Auth::id()
                    ]);

                    Log::info('Created ELISA test entry for sub mini pool', [
                        'sub_mini_pool_number' => $subMiniPoolNumber,
                        'user' => Auth::user()->name ?? 'Unknown'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error creating ELISA test entries: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    /**
     * Upload and process ELISA test results for sub mini pools
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadResults(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'result_file' => 'required|file|mimes:pdf|max:10240',
                'test_type' => 'required|string|in:hbv,hcv,hiv'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the uploaded file
            $file = $request->file('result_file');
            $testType = strtolower($request->input('test_type'));

            // Process PDF file (extract results for viewing only)
            $results = $this->processPDFFile($file);

            return response()->json([
                'status' => 'success',
                'message' => "Successfully processed {$testType} results for viewing",
                'data' => [
                    'test_type' => $testType,
                    'results' => $results,
                    'extracted_count' => count($results)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading sub mini pool results: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process PDF file for ELISA test results using Python script
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function processPDFFile($file)
    {
        $results = [];
        Log::info('Processing PDF file for ELISA results', [
            'file' => $file->getClientOriginalName()
        ]);

        try {
            // Store the uploaded file temporarily
            $originalName = $file->getClientOriginalName();
            $uniqueFilename = time() . '_' . uniqid() . '_' . $originalName;
            $path = $file->storeAs('temp', $uniqueFilename, 'public');
            $fullPath = storage_path('app/public/' . $path);

            // Process PDF file using Python script (same as ELISA upload)
            $pythonResult = $this->parseResFileWithPython($fullPath);

            // Clean up temporary file
            \Storage::disk('public')->delete($path);

            // Check if there was an error
            if (isset($pythonResult['error']) || !isset($pythonResult['sample_results'])) {
                Log::error('Python script returned error or invalid format:', [
                    'file' => $originalName,
                    'error' => $pythonResult['error'] ?? 'Invalid response format',
                    'result_keys' => is_array($pythonResult) ? array_keys($pythonResult) : 'not an array'
                ]);
                throw new \Exception('Error processing PDF file: ' . ($pythonResult['error'] ?? 'Invalid response format'));
            }

            // Process ALL sample results from PDF (like ELISA upload)
            $sampleResults = $pythonResult['sample_results'] ?? [];

            foreach ($sampleResults as $sample) {
                $patientId = $sample['Patient ID'] ?? '';

                // Determine result based on the Result field
                $resultText = strtolower(trim($sample['Result'] ?? ''));
                $result = 'nonreactive'; // default

                if (strpos($resultText, 'reactive') !== false) {
                    $result = 'reactive';
                } elseif (strpos($resultText, 'borderline') !== false) {
                    $result = 'borderline';
                } elseif (strpos($resultText, 'non-reactive') !== false || strpos($resultText, 'nonreactive') !== false) {
                    $result = 'nonreactive';
                }

                // Use Patient ID as the key (this is what gets stored in elisa_test_report.mini_pool_id)
                $results[$patientId] = [
                    'well_num' => $sample['Well'] ?? '',
                    'od_value' => $sample['ODValue'] ?? '',
                    'result' => $result,
                    'ratio' => $sample['Ratio'] ?? '',
                    'patient_id' => $patientId
                ];
            }

            Log::info('PDF processing complete', [
                'matches_found' => count($results),
                'sample_results_count' => count($sampleResults)
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing PDF file: ' . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Parse PDF file using Python script (same as ELISA upload)
     *
     * @param string $filePath
     * @return array
     */
    private function parseResFileWithPython($filePath)
    {
        try {
            // Use Python script to parse PDF file
            $pythonExec = env('PYTHON_PATH', 'C:\\Users\\salma\\AppData\\Local\\Programs\\Python\\Python313\\python.exe');
            $script = public_path('parse_res.py');
            $cmd = $this->quoteWin($pythonExec)
                   . ' ' . $this->quoteWin($script)
                   . ' ' . $this->quoteWin($filePath)
                   . ' 2>&1';

            // Execute the Python script
            $raw = shell_exec($cmd);
            $parsedData = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error parsing PDF file:', [
                    'file' => $filePath,
                    'cmd' => $cmd,
                    'raw' => $raw,
                    'json_error' => json_last_error_msg()
                ]);
                throw new \Exception("Error parsing PDF: " . json_last_error_msg());
            }

            return $parsedData;

        } catch (\Exception $e) {
            Log::error('Error in Python script execution: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Quote Windows paths for command line execution
     *
     * @param string $path
     * @return string
     */
    private function quoteWin($path)
    {
        return '"' . str_replace('"', '""', $path) . '"';
    }

    /**
     * Save extracted results to database (sub_mini_pool_elisa_test_report table)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveResults(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'results' => 'required|array',
                'results.*.sub_mini_pool_id' => 'required|string',
                'results.*.mini_pool_number' => 'nullable|string',
                'results.*.well_num' => 'nullable|string',
                'results.*.od_value' => 'nullable|string',
                'results.*.hbv' => 'nullable|string',
                'results.*.hcv' => 'nullable|string',
                'results.*.hiv' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $results = $request->input('results');
            $savedCount = 0;

            foreach ($results as $result) {
                $subMiniPoolId = $result['sub_mini_pool_id'];

                // Calculate final result based on all three test types
                $finalResult = 'Nonreactive';
                if (!empty($result['hbv']) && $result['hbv'] === 'reactive' ||
                    !empty($result['hcv']) && $result['hcv'] === 'reactive' ||
                    !empty($result['hiv']) && $result['hiv'] === 'reactive') {
                    $finalResult = 'Reactive';
                } else if (!empty($result['hbv']) && $result['hbv'] === 'borderline' ||
                           !empty($result['hcv']) && $result['hcv'] === 'borderline' ||
                           !empty($result['hiv']) && $result['hiv'] === 'borderline') {
                    $finalResult = 'Borderline';
                }

                // Check if entry exists in sub_mini_pool_elisa_test_report
                $elisaReport = DB::table('sub_mini_pool_elisa_test_report')
                    ->where('sub_mini_pool_id', $subMiniPoolId)
                    ->first();

                if (!$elisaReport) {
                    // Create new entry
                    DB::table('sub_mini_pool_elisa_test_report')->insert([
                        'sub_mini_pool_id' => $subMiniPoolId,
                        'mini_pool_number' => $result['mini_pool_number'] ?? $subMiniPoolId,
                        'well_num' => $result['well_num'] ?? null,
                        'od_value' => $result['od_value'] ?? null,
                        'ratio' => '1.0',
                        'hbv' => !empty($result['hbv']) ? $result['hbv'] : null,
                        'hcv' => !empty($result['hcv']) ? $result['hcv'] : null,
                        'hiv' => !empty($result['hiv']) ? $result['hiv'] : null,
                        'final_result' => $finalResult,
                        'result_time' => now(),
                        'timestamp' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    // Update existing entry
                    $updateData = [
                        'mini_pool_number' => $result['mini_pool_number'] ?? $elisaReport->mini_pool_number,
                        'well_num' => $result['well_num'] ?? $elisaReport->well_num,
                        'od_value' => $result['od_value'] ?? $elisaReport->od_value,
                        'result_time' => now(),
                        'updated_at' => now(),
                        'updated_by' => Auth::id()
                    ];

                    // Update test results if they exist
                    if (!empty($result['hbv'])) {
                        $updateData['hbv'] = $result['hbv'];
                    }
                    if (!empty($result['hcv'])) {
                        $updateData['hcv'] = $result['hcv'];
                    }
                    if (!empty($result['hiv'])) {
                        $updateData['hiv'] = $result['hiv'];
                    }

                    // Update final_result
                    $updateData['final_result'] = $finalResult;

                    // Update the database
                    DB::table('sub_mini_pool_elisa_test_report')
                        ->where('sub_mini_pool_id', $subMiniPoolId)
                        ->update($updateData);
                }

                $savedCount++;
            }

            return response()->json([
                'status' => 'success',
                'message' => "Successfully saved $savedCount results to database",
                'saved_count' => $savedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving sub mini pool results: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all ELISA test results for sub mini pools from sub_mini_pool_elisa_test_report table
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllResults()
    {
        try {
            // Get all sub mini pool ELISA test reports
            $results = DB::table('sub_mini_pool_elisa_test_report as smpetr')
                ->select(
                    'smpetr.sub_mini_pool_id',
                    'smpetr.mini_pool_number',
                    'smpetr.well_num',
                    'smpetr.od_value',
                    'smpetr.ratio',
                    'smpetr.hbv',
                    'smpetr.hcv',
                    'smpetr.hiv',
                    'smpetr.final_result'
                )
                ->whereNotNull('smpetr.sub_mini_pool_id')
                ->where('smpetr.sub_mini_pool_id', '!=', '')
                ->orderBy('smpetr.sub_mini_pool_id')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching all sub mini pool results: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate the new final result based on the test type and result
     *
     * @param string $testType
     * @param string $result
     * @param object|null $currentReport
     * @return string|null
     */
    private function calculateNewFinalResult($testType, $result, $currentReport = null)
    {
        if (!$currentReport) {
            return strtolower($result) == 'reactive' ? 'Reactive' : 'Nonreactive';
        }

        // Get current values
        $hbv = $testType == 'hbv' ? strtolower($result) : $currentReport->hbv;
        $hcv = $testType == 'hcv' ? strtolower($result) : $currentReport->hcv;
        $hiv = $testType == 'hiv' ? strtolower($result) : $currentReport->hiv;

        // If any test is reactive, the final result is reactive
        if (
            $hbv == 'reactive' ||
            $hcv == 'reactive' ||
            $hiv == 'reactive'
        ) {
            return 'Reactive';
        }

        // If all tests are nonreactive, the final result is nonreactive
        if (
            ($hbv == 'nonreactive' || $hbv === null) &&
            ($hcv == 'nonreactive' || $hcv === null) &&
            ($hiv == 'nonreactive' || $hiv === null) &&
            // At least one test must be nonreactive (not all can be null)
            ($hbv == 'nonreactive' || $hcv == 'nonreactive' || $hiv == 'nonreactive')
        ) {
            return 'Nonreactive';
        }

        // If we can't determine the final result, return null
        return null;
    }

    /**
     * Get mini pools that have sub mini pool test results
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMiniPoolsWithResults()
    {
        try {
            $miniPools = DB::table('sub_mini_pool_elisa_test_report')
                ->select('mini_pool_number')
                ->whereNotNull('mini_pool_number')
                ->where('mini_pool_number', '!=', '')
                ->distinct()
                ->orderBy('mini_pool_number')
                ->pluck('mini_pool_number');

            return response()->json([
                'status' => 'success',
                'data' => $miniPools
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching mini pools with results: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch mini pools: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sub mini pools by mini pool number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubMiniPoolsByMiniPool(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mini Pool Number is required'
                ], 400);
            }

            $subMiniPools = DB::table('sub_mini_pool_elisa_test_report')
                ->select('sub_mini_pool_id', 'mini_pool_number')
                ->where('mini_pool_number', $request->mini_pool_number)
                ->orderBy('sub_mini_pool_id')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $subMiniPools
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching sub mini pools: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sub mini pools: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report data for sub mini pools
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReportData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_number' => 'required|string',
                'sub_mini_pool_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = DB::table('sub_mini_pool_elisa_test_report')
                ->where('mini_pool_number', $request->mini_pool_number);

            // Filter by specific sub mini pool if provided
            if ($request->filled('sub_mini_pool_id')) {
                $query->where('sub_mini_pool_id', $request->sub_mini_pool_id);
            }

            $results = $query->orderBy('sub_mini_pool_id')->get();

            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching report data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch report data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMiniPoolBagDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mini Pool Number is required'
                ], 400);
            }

            // Get bag entries detail IDs from bag_entries_mini_pools
            $bagEntriesMiniPool = DB::table('bag_entries_mini_pools')
                ->where('mini_pool_number', $request->mini_pool_number)
                ->first();

            if (!$bagEntriesMiniPool) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No bag entries found for this mini pool'
                ], 404);
            }

            // Get the bag entries detail IDs
            $bagEntriesDetailIds = explode(',', trim($bagEntriesMiniPool->bag_entries_detail_ids, '[]'));

            // Fetch bag entry details
            $bagEntryDetails = DB::table('bag_entries_details')
                ->whereIn('id', $bagEntriesDetailIds)
                ->get();

            // Get bag entry data
            $bagDetails = [];
            if ($bagEntryDetails->count() > 0) {
                foreach ($bagEntryDetails as $detail) {
                    $bagDetails[] = [
                        'donor_id' => $detail->donor_id ?? '',
                        'donation_date' => $detail->donation_date ?? '',
                        'blood_group' => $detail->blood_group ?? '',
                        'bag_volume' => $detail->bag_volume_ml ?? '',
                        'tail_cutting' => $detail->tail_cutting ?? 'No'
                    ];
                }
            }

            // Get sub mini pool numbers
            $subMiniPoolEntry = SubMiniPoolEntry::where('mini_pool_number', $request->mini_pool_number)
                ->first();

            $subMiniPoolNumbers = [];
            if ($subMiniPoolEntry) {
                $subMiniPoolNumbers = explode(',', $subMiniPoolEntry->sub_mini_pool_no);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'bag_details' => $bagDetails,
                    'sub_mini_pool_numbers' => $subMiniPoolNumbers,
                    'row_count' => count($bagEntriesDetailIds)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mini pool bag details: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch mini pool bag details: ' . $e->getMessage()
            ], 500);
        }
    }
}
