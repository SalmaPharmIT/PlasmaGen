<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PrinterLabelController extends Controller
{
    /**
     * The API endpoint for the BarTender Integration web service.
     * This MUST be configured in your .env file.
     *
     * Example .env entry:
     * BARTENDER_API_URL=https://bartender.yourcompany.com/integration/labelprint
     */
    protected $bartenderApiUrl;

    public function __construct()
    {
        // Load the BarTender API URL from the environment configuration.
        $this->bartenderApiUrl = config('services.bartender.api_url');
    }

    /**
     * Creates a print job by sending data to the BarTender Web Service API.
     * This is the correct method for when your app and BarTender are in different environments.
     */
    public function printWithBarTender(Request $request)
    {
        // Step 1: Validate the incoming data from the form.
        try {
            $validated = $request->validate([
                'ar_number' => 'required|string|max:255',
                'ref_number' => 'required|string|max:255',
                'mega_pool' => 'required|string|max:255',
                'mini_pools' => 'required|array',
                'mini_pools.*' => 'required|string|max:255',
            ]);

            // Check if the API URL has been set in the .env file.
            if (!$this->bartenderApiUrl) {
                throw new \Exception("The BarTender API URL is not configured. Please set BARTENDER_API_URL in your .env file.");
            }

            // Step 2: Structure the print job data into a JSON format.
            $printJobs = [];

            // Add the Mega Pool job (2 copies).
            $printJobs[] = [
                // The keys here (e.g., 'ar_number') MUST match the "Named Data Sources"
                // in your BarTender label template.
                'ar_number'  => $validated['ar_number'],
                'ref_number' => $validated['ref_number'],
                'pool_type'  => 'Mega Pool',
                'pool_id'    => $validated['mega_pool'],
                'barcode_data' => $validated['mega_pool'], // This will be used for the barcode
                'quantity'   => 2, // Tells BarTender to print 2 copies
            ];

            // Add the Mini Pool jobs (1 copy each).
            foreach ($validated['mini_pools'] as $miniPool) {
                $printJobs[] = [
                    'ar_number'  => $validated['ar_number'],
                    'ref_number' => $validated['ref_number'],
                    'pool_type'  => 'Mini Pool',
                    'pool_id'    => $miniPool,
                    'barcode_data' => $miniPool, // This will be used for the barcode
                    'quantity'   => 1,
                ];
            }

            // This is the final payload that will be sent to BarTender.
            // Your BarTender integration should be configured to expect this structure.
            $payload = [
                'LabelTemplate' => 'PlasmaPoolLabels.btw', // Update this to your actual template filename
                'PrintJobs' => $printJobs,
            ];

            // Step 3: Send the data to the BarTender API using Laravel's HTTP Client.
            Log::info("Sending print job to BarTender API: " . $this->bartenderApiUrl);
            $response = Http::timeout(30)->post($this->bartenderApiUrl, $payload);

            // Step 4: Handle the response from BarTender.
            if (!$response->successful()) {
                Log::error("BarTender API request failed.", ['status' => $response->status(), 'body' => $response->body()]);
                throw new \Exception("The BarTender service responded with an error. Status: " . $response->status());
            }

            Log::info("Successfully sent print job to BarTender API.");

            // Return a success message to the frontend.
            return response()->json([
                'success' => true,
                'message' => 'Print job successfully submitted to BarTender.',
            ]);

        } catch (ValidationException $e) {
            // Handle cases where the form data is invalid.
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle all other errors (e.g., connection failed, URL not set).
            Log::error("BarTender API integration error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download individual CSV file to the user's browser
     */
    public function downloadCsv(Request $request)
    {
        try {
            $validated = $request->validate([
                'ar_number' => 'required|string|max:255',
                'ref_number' => 'required|string|max:255',
                'mega_pool' => 'required|string|max:255',
                'mini_pools' => 'required|array',
                'mini_pools.*' => 'required|string|max:255',
                'file_type' => 'required|string|in:MEGA,MINI,POOL',
                'print_number' => 'required|integer|min:1|max:4',
                'print_word' => 'required|string'
            ]);

            // Create CSV content with comma-separated values
            // No header row, just data
            $csvContent = $validated['ar_number'] . "," . $validated['ref_number'] . "," . $validated['mega_pool'];

            // Add all mini pools to the same row, comma-separated
            foreach ($validated['mini_pools'] as $miniPool) {
                $csvContent .= "," . $miniPool;
            }

            // Add a newline at the end
            $csvContent .= "\n";

            // Generate filename based on print_word
            $fileName = ($validated['print_word'] === 'reprint' ? 'REPRINT.csv' : 'PRINT.csv');

            // Log the content being downloaded
            Log::info("CSV content for {$fileName} being downloaded: " . $csvContent);

            // Set headers for CSV download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            // Return the CSV content as a downloadable file
            return response($csvContent, 200, $headers);
        } catch (ValidationException $e) {
            Log::error("CSV validation error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("CSV download error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Try an alternative method to save files if the standard method fails
     */
    private function tryAlternativeFileSave($content, $paths)
    {
        $savedFiles = [];

        foreach ($paths as $path) {
            try {
                $directory = $path['dir'];
                $baseFileName = pathinfo($path['file'], PATHINFO_FILENAME);
                $extension = pathinfo($path['file'], PATHINFO_EXTENSION);

                // Create directory if it doesn't exist
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }

                // Use a different file path format
                $timestamp = date('YmdHis');
                $filePath = $directory . '\\' . $baseFileName . '_' . $timestamp . '.' . $extension;

                // Try a different method to write the file
                $file = fopen($filePath, 'w');
                if ($file) {
                    fwrite($file, $content);
                    fclose($file);
                    $savedFiles[] = $filePath;
                    Log::info("Alternative save method succeeded for: " . $filePath);
                }
            } catch (\Exception $e) {
                Log::error("Alternative save method failed: " . $e->getMessage());
            }
        }

        return $savedFiles;
    }
}
