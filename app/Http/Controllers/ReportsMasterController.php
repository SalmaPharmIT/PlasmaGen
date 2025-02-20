<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class ReportsMasterController extends Controller
{

    /**
     * Show the entities list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        return view('reports.reports_work_summary');
    }

    public function getPeriodicWorkSummaryData(Request $request)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching entities
        $apiUrl = config('auth_api.reports_periodic_work_summary_url');

        if (!$apiUrl) {
            Log::error('Periodic Work Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Periodic Work Summary fetch URL is not configured.'
            ], 500);
        }

        // Retrieve filter parameters from the request
        $agent_id = $request->input('collectingAgent'); // Agent ID from dropdown
        $dateRange = $request->input('dateRange'); // Date range in "YYYY-MM-DD - YYYY-MM-DD" format

        // Prepare the payload to submit to the external API
        $payload = [
            'agent_id'   => $agent_id,
            'dateRange' => $dateRange,
        ];

         // Log the data being sent
         Log::info('getPeriodicWorkSummaryData request API', [
            'data' => $payload,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $payload);

            Log::info('External API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'data' => Arr::get($apiResponse, 'data', []),
                    ]);
                } else {
                    Log::warning('External API returned failure.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch Periodic Work Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Periodic Work Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Periodic Work Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Periodic Work Summary.',
            ], 500);
        }
    }


}
