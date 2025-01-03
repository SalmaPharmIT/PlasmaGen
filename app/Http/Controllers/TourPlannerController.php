<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class TourPlannerController extends Controller
{
   
    /**
     * Show the tour plan list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('tourplanner.index');
    }

    /**
     * API Endpoint to Fetch All Collecting Agents Users Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getCollectingAgents()
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
        $apiUrl = config('auth_api.getAllCollectingAgents_url');

        if (!$apiUrl) {
            Log::error('Collecting Agents fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Collecting Agents fetch URL is not configured.'
            ], 500);
        }

      //  Log::info('Fetching collecting agents from external API.', ['api_url' => $apiUrl]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

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
                Log::error('Failed to fetch collecting agents from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch collecting agents from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching collecting agents from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching collecting agents.',
            ], 500);
        }
    }


     /**
     * API Endpoint to Fetch Calendar Events based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        // Retrieve filters from the request
        $agentId = $request->input('agent_id');
        $month = $request->input('month'); // Expected format: YYYY-MM

        // Validate inputs
        if (!$month) {
            return response()->json([
                'success' => false,
                'message' => 'Month is required.'
            ], 400);
        }

        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching calendar events
        $apiUrl = config('auth_api.tour_plan_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Calendar Events fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Calendar Events fetch URL is not configured.'
            ], 500);
        }

        try {
            // Build query parameters
            $queryParams = [
                'month' => $month,
            ];

            if ($agentId) {
                $queryParams['agent_id'] = $agentId;
            }

            // Log the data being sent
            Log::info('Fetch Tour Plan request API', [
                'data' => $queryParams,
            ]);


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response for Calendar Events', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    // Assuming the API returns events in FullCalendar-compatible format
                    return response()->json([
                        'success' => true,
                        'events' => Arr::get($apiResponse, 'data', []),
                    ]);
                } else {
                    Log::warning('External API returned failure for Calendar Events.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch calendar events from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch calendar events from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching calendar events from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching calendar events.',
            ], 500);
        }
    }


   /**
    * API Endpoint to Save a Tour Plan.
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function saveTourPlan(Request $request)
    {
        
        // Validate the form data
        $data = $request->validate([
            'blood_bank_id' => 'required|integer|exists:entities,id', // Assuming 'entities' table holds blood banks
            'date' => 'required|date',
            'collecting_agent_id' => 'required|integer|exists:users,id', // Assuming 'users' table holds collecting agents
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for saving the tour plan
        $apiUrl = config('auth_api.tour_plan_create_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('Save Tour Plan URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Save Tour Plan URL is not configured.'
            ], 500);
        }

        try {
        // Include additional fields if necessary
        // For example, 'status' => 'submitted' by default
        $payload = [
            'blood_bank_id' => $data['blood_bank_id'],
            'date' => $data['date'],
            'collecting_agent_id' => $data['collecting_agent_id'],
            'quantity' => $data['quantity'],
            'remarks' => $data['remarks'] ?? null,
            'status' => 'submitted', // default status
            'created_by' => Auth::id(), // assuming user is authenticated
        ];

        // Include optional fields if provided
        if (isset($data['latitude'])) {
            $payload['latitude'] = $data['latitude'];
        }
        if (isset($data['longitude'])) {
            $payload['longitude'] = $data['longitude'];
        }

        // Log the data being sent
        Log::info('Sending data to Tour Plan Create API', [
            'data' => $payload,
        ]);

        // Send the data to the external API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post($apiUrl, $payload);

        Log::info('External API Response for Save Tour Plan', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->successful()) {
        $apiResponse = $response->json();

        if (Arr::get($apiResponse, 'success')) {
            return response()->json([
                'success' => true,
                'message' => Arr::get($apiResponse, 'message', 'Tour Plan saved successfully.')
            ]);
        } else {
            Log::warning('External API returned failure for Save Tour Plan.', ['message' => Arr::get($apiResponse, 'message')]);
            return response()->json([
                'success' => false,
                'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.')
            ]);
        }
        } else {
            Log::error('Failed to save tour plan via external API.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save tour plan via the external API.'
            ], $response->status());
        }
        } catch (\Exception $e) {
            Log::error('Exception while saving tour plan via external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the tour plan.'
            ], 500);
        }
    }


     /**
     * API Endpoint to Fetch All Blood Banks Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getBloodBanks()
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

        // Define the external API URL for fetching blood banks
        $apiUrl = config('auth_api.blood_bank_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Blood Banks fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Blood Banks fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

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
                Log::error('Failed to fetch blood banks from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch blood banks from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching blood banks from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching blood banks.',
            ], 500);
        }
    }

    /**
     * API Endpoint to Delete a Tour Plan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTourPlan(Request $request)
    {
        // Validate the request
        $request->validate([
            'tour_plan_id' => 'required|integer',
        ]);

        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for deleting the tour plan
        $apiUrl = config('auth_api.tour_plan_delete_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('Delete Tour Plan URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Delete Tour Plan URL is not configured.'
            ], 500);
        }

        try {
            // Send the DELETE request to the external API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->delete($apiUrl, [
                'tour_plan_id' => $request->input('tour_plan_id'),
            ]);

            Log::info('External API Response for Delete Tour Plan', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'message' => Arr::get($apiResponse, 'message', 'Tour Plan deleted successfully.')
                    ]);
                } else {
                    Log::warning('External API returned failure for Delete Tour Plan.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.')
                    ]);
                }
            } else {
                Log::error('Failed to delete tour plan via external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete tour plan via the external API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while deleting tour plan via external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the tour plan.'
            ], 500);
        }
    }

}
