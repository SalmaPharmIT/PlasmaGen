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
        
        // Validate the form data with conditional rules
        $validatedData = $request->validate([
            'tour_plan_type' => 'required|in:collections,sourcing',

            // Fields required for Collections
            'blood_bank_id' => 'nullable|required_if:tour_plan_type,collections|integer|exists:entities,id',
            'date' => 'required|date',
            'time' => 'nullable|required_if:tour_plan_type,collections|date_format:H:i',
          //  'collecting_agent_id' => 'nullable|required_if:tour_plan_type,collections|integer|exists:users,id',
            'pending_documents_id' => 'nullable|array',
            'quantity' => 'nullable|required_if:tour_plan_type,collections|integer|min:1',

            // Fields required for Sourcing
            'sourcing_blood_bank_name' => 'nullable|required_if:tour_plan_type,sourcing|string|max:255',
            'sourcing_city_id' => 'nullable|required_if:tour_plan_type,sourcing|integer|exists:cities,id',

            // Common Fields
            'collecting_agent_id' => 'required|integer|exists:users,id',
            'collections_remarks' => 'nullable|string',
            'sourcing_remarks' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

         // Log the data being sent
         Log::info('Tour Plan Create API validatedData', [
            'validatedData' => $validatedData,
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
        // Initialize the payload with common fields
        $payload = [
            'tour_plan_type' => $validatedData['tour_plan_type'],
            'date' => $validatedData['date'],
            'remarks' => $validatedData['collections_remarks'] ?? $validatedData['sourcing_remarks'] ?? null,
            'status' => 'initiated', // default status
            'created_by' => Auth::id(), // assuming user is authenticated
            'collecting_agent_id' => $validatedData['collecting_agent_id'], // **Mapped to employee_id**
        ];

        // Conditionally add fields based on tour_plan_type
        if ($validatedData['tour_plan_type'] === 'collections') {
            $payload['blood_bank_id'] = $validatedData['blood_bank_id'];
            $payload['time'] = $validatedData['time'];
            $payload['quantity'] = $validatedData['quantity'];
           
            // Convert array to comma-separated string for pending documents
            if (!empty($validatedData['pending_documents_id'])) {
                $payload['pending_documents_id'] = implode(',', $validatedData['pending_documents_id']);
            }
        }

        if ($validatedData['tour_plan_type'] === 'sourcing') {
            $payload['sourcing_blood_bank_name'] = $validatedData['sourcing_blood_bank_name'];
            $payload['sourcing_city_id'] = $validatedData['sourcing_city_id'];
        }

        // Include optional fields if provided
        if (!empty($validatedData['latitude'])) {
            $payload['latitude'] = $validatedData['latitude'];
        }
        if (!empty($validatedData['longitude'])) {
            $payload['longitude'] = $validatedData['longitude'];
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


    /**
     * Show the manage tour plan list page.
     *
     * @return \Illuminate\View\View
     */
    public function manage()
    {
        return view('tourplanner.manage');
    }

    
    /**
     * API Endpoint to Fetch All Pending Documents Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getPendingDocuments()
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        Log::info('getPendingDocuments function invoked');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching Pending Documents
        $apiUrl = config('auth_api.pending_documents_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Pending Documents fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Pending Documents fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response Pending Documents', [
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
                Log::error('Failed to fetch pending documents from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch pending documents  from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching pending documents  from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching pending documents .',
            ], 500);
        }
    }


    /**
         * Show the TP Collection Requests to Logistics Admin.
         *
         * @return \Illuminate\View\View
    */
    public function collectionrequests()
    {
        return view('tourplanner.collectionrequests');
    }


    /**
     * API Endpoint to Fetch Collection Requests.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getCollectionRequests()
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
        $apiUrl = config('auth_api.collection_requests_all_url');

        if (!$apiUrl) {
            Log::error('Collection Requests fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Collection Requests fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching Collection Requests from external API.', ['api_url' => $apiUrl]);

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

                Log::warning('External API Collection Requests apiResponse', ['apiResponse' => $apiResponse]);

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
                Log::error('Failed to fetch collection requests from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch collection requests  from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching collection requests  from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching collection requests.',
            ], 500);
        }
    }

    /**
     * API Endpoint to Submit Vehicle Details
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function submitVehicleDetails(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'collection_request_id' => 'required|integer|exists:tour_plan,id',
            'vehicle_number' => 'required|string|max:50',
            'driver_name' => 'required|string|max:100',
            'contact_number' => 'required|string|max:15',
            'email_id' => 'nullable|string',
            'alternate_mobile_no' => 'nullable|string|max:15',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Retrieve the API token from the session
        $token = session()->get('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for submitting vehicle details
        $apiUrl = config('auth_api.vehicle_details_submit_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('Vehicle Details Submit URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Vehicle Details Submit URL is not configured.'
            ], 500);
        }

         // Step 3: Map form fields to transport_details table fields
         $transportData = [
            'collection_request_id' => $validatedData['collection_request_id'],
            'vehicle_number' => $validatedData['vehicle_number'],
            'driver_name' => $validatedData['driver_name'],
            'contact_number' => $validatedData['contact_number'],
            'alternative_contact_number' => $validatedData['alternate_mobile_no'] ?? null,
            'email_id' => $validatedData['email_id'],
            'remarks' => $validatedData['remarks'] ?? null,
            'created_by' => Auth::id(), // assuming user is authenticated
           // 'modified_by' => Auth::id(),
        ];

        Log::info('Vehicle Details Request external API.', ['transportData' => $transportData]);


        try {
            // Send the data to the external API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $transportData);

            Log::info('External API Response for Vehicle Details Submission', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'message' => Arr::get($apiResponse, 'message', 'Vehicle details submitted successfully.')
                    ]);
                } else {
                    Log::warning('External API returned failure for Vehicle Details Submission.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.')
                    ]);
                }
            } else {
                Log::error('Failed to submit vehicle details via external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit vehicle details via the external API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while submitting vehicle details via external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting vehicle details.'
            ], 500);
        }
    }

    /**
     * Show the TP Collection Requests Manageto Logistics Admin.
     *
     * @return \Illuminate\View\View
    */
    public function collectionsManage()
    {
        return view('tourplanner.collectionsmanage');
    }

    /**
     * API Endpoint to Fetch Collection Submitted.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getCollectionSubmitted(Request $request)
    {

        // Retrieve filter parameters from the request
        $agentId = $request->input('agent_id');
        $month = $request->input('month'); // Expected format: YYYY-MM

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
        $apiUrl = config('auth_api.collection_submitted_all_url');

        if (!$apiUrl) {
            Log::error('Collection Submitted fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Collection Submitted fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching Collection Submitted from external API.', ['api_url' => $apiUrl]);

        try {

            // Build query parameters
            $queryParams = [];

            if ($agentId) {
                $queryParams['agent_id'] = $agentId;
            }

            if ($month) {
                $queryParams['month'] = $month;
            }

            // Log the data being sent
            Log::info('Fetch Collection Submitted request API', [
                'data' => $queryParams,
            ]);

            // Send the GET request with query parameters
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                Log::warning('External API Collection Submitted apiResponse', ['apiResponse' => $apiResponse]);

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
                Log::error('Failed to fetch collection Submitted from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch collection Submitted  from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching collection Submitted  from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching collection Submitted.',
            ], 500);
        }
    }

}
