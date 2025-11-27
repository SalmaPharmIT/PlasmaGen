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
        $apiUrl = config('auth_api.collecting_agents_fetch_by_managerId_url');

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
            'tour_plan_type' => 'required|in:collections,sourcing,both',

            // Fields required for Collections
            'blood_bank_id' => 'nullable|required_if:tour_plan_type,collections|required_if:tour_plan_type,both|integer|exists:entities,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
          //  'time' => 'nullable|required_if:tour_plan_type,collections|required_if:tour_plan_type,both|date_format:H:i',
          //  'collecting_agent_id' => 'nullable|required_if:tour_plan_type,collections|integer|exists:users,id',
            'pending_documents_id' => 'nullable|array',
            'quantity' => 'nullable|required_if:tour_plan_type,collections|required_if:tour_plan_type,both|integer|min:1',

            // Fields required for Sourcing
            'sourcing_blood_bank_name' => 'nullable|string|max:255',
            'sourcing_city_id' => 'nullable|required_if:tour_plan_type,sourcing|integer|exists:cities,id',

            // Common Fields
            'collecting_agent_id' => 'required|integer|exists:users,id',
            // 'collections_remarks' => 'nullable|string',
            // 'sourcing_remarks' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'remarks' => 'nullable|string',
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
           // 'remarks' => $validatedData['collections_remarks'] ?? $validatedData['sourcing_remarks'] ?? null,
            'remarks' => $validatedData['remarks'] ?? null, // Single common remarks field
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
           // $payload['sourcing_blood_bank_name'] = $validatedData['sourcing_blood_bank_name'];
            $payload['sourcing_city_id'] = $validatedData['sourcing_city_id'];
        }

        if ($validatedData['tour_plan_type'] === 'both') {
            $payload['blood_bank_id'] = $validatedData['blood_bank_id'];
            $payload['time'] = $validatedData['time'];
            $payload['quantity'] = $validatedData['quantity'];
           
            // Convert array to comma-separated string for pending documents
            if (!empty($validatedData['pending_documents_id'])) {
                $payload['pending_documents_id'] = implode(',', $validatedData['pending_documents_id']);
            }

        //    $payload['sourcing_city_id'] = $validatedData['sourcing_city_id'];
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
            'warehouse_id'          => 'required|integer|exists:entities,id',
            'transport_partner_id'  => 'required|integer|exists:transport_partners,id',
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
            'warehouse_id'          => $validatedData['warehouse_id'],
            'transport_partner_id'  => $validatedData['transport_partner_id'],
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


    /**
     * Show the  tour plan DCR Requests list page.
     *
     * @return \Illuminate\View\View
     */
    public function dcrRequests()
    {
        return view('tourplanner.dcr');
    }

    /**
     * API Endpoint to Fetch DCRApprovals based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDCRApprovals(Request $request)
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
        $apiUrl = config('auth_api.dcr_approvals_fetch_all_url');

        if (!$apiUrl) {
            Log::error('DCR Approvals fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'DCR Approvals fetch URL is not configured.'
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
            Log::info('Fetch DCR Approvals request API', [
                'data' => $queryParams,
            ]);


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response for DCR Approvals', [
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
                    Log::warning('External API returned failure for DCR Approvals.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch DCR Approvals from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch DCR Approvals from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching DCR Approvals from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching DCR Approvals.',
            ], 500);
        }
    }


    /**
     * Display the specified DCR details by fetching data from an external API.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    // public function showDCRDetails($id)
    // {
    //     // 1. Validate the input
    //     if (!$id || !is_numeric($id)) {
    //         return redirect()->back()->with('error', 'Visit ID is required and must be a valid number.');
    //     }

    //     // 2. Retrieve the token from the session
    //     $token = session()->get('api_token');

    //     if (!$token) {
    //         Log::warning('API token missing in session.');
    //         return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
    //     }

    //     // 3. Define the external API URL for fetching DCR details
    //     $apiUrl = config('auth_api.dcr_details_url');

    //     if (!$apiUrl) {
    //         Log::error('DCR details fetch URL not configured.');
    //         return redirect()->back()->with('error', 'DCR details fetch URL is not configured.');
    //     }

    //     try {
    //         // 4. Build query parameters
    //         $queryParams = [
    //             'id' => $id,
    //         ];

    //         // 5. Log the request data
    //         Log::info('Fetch DCR Details request API', [
    //             'data' => $queryParams,
    //         ]);

    //         // 6. Make the API call
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . $token,
    //             'Accept' => 'application/json',
    //         ])->get($apiUrl, $queryParams);

    //         // 7. Log the API response
    //         Log::info('External API Response for DCR Details', [
    //             'status' => $response->status(),
    //             'body' => $response->body(),
    //         ]);

    //         // 8. Handle successful API response
    //         if ($response->successful()) {
    //             $apiResponse = $response->json();

    //             if (Arr::get($apiResponse, 'success') && is_array(Arr::get($apiResponse, 'data')) && count(Arr::get($apiResponse, 'data')) > 0) {
    //                 // Assuming the API returns an array with at least one DCR detail
    //                 $dcr = $apiResponse['data'][0];

    //                 // 9. Determine the tour_plan_type
    //                 $tourPlanType = Arr::get($dcr, 'extendedProps.tour_plan_type');

    //                 if ($tourPlanType == 1) {
    //                     // Collections
    //                     return view('tourplanner.dcrCollections', ['dcr' => $dcr]);
    //                 } elseif ($tourPlanType == 2) {
    //                     // Sourcing
    //                     return view('tourplanner.dcrSourcing', ['dcr' => $dcr]);
    //                 } else {
    //                     Log::warning('Invalid DCR type received from API.', [
    //                         'tour_plan_type' => $tourPlanType,
    //                     ]);
    //                     return redirect()->back()->with('error', 'Invalid DCR type received.');
    //                 }
    //             } else {
    //                 // API returned success: false or no data
    //                 $message = Arr::get($apiResponse, 'message', 'Failed to fetch DCR details.');
    //                 Log::warning('External API returned failure for DCR Details.', ['message' => $message]);
    //                 return redirect()->back()->with('error', $message);
    //             }
    //         } else {
    //             // API call failed (non-2xx status code)
    //             $status = $response->status();
    //             $error = $response->body();

    //             Log::error('Failed to fetch DCR Details from external API.', [
    //                 'status' => $status,
    //                 'body' => $error,
    //             ]);

    //             return redirect()->back()->with('error', 'Failed to fetch DCR Details from the external API.');
    //         }
    //     } catch (\Exception $e) {
    //         // Handle exceptions, such as network issues
    //         Log::error('Exception while fetching DCR Details from external API.', ['error' => $e->getMessage()]);
    //         return redirect()->back()->with('error', 'An error occurred while fetching DCR Details.');
    //     }
    // }

    public function showDCRDetails($id)
    {
        // Validate the input
        if (!$id || !is_numeric($id)) {
            return response()->json(['error' => 'Visit ID is required and must be a valid number.'], 400);
        }

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json(['error' => 'Authentication token missing. Please log in again.'], 401);
        }

        // Define the external API URL for fetching DCR details
        $apiUrl = config('auth_api.dcr_details_url');
        if (!$apiUrl) {
            return response()->json(['error' => 'DCR details fetch URL is not configured.'], 500);
        }

        try {
            $queryParams = ['id' => $id];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            // Log the data being sent
            Log::info('DCR Details API apiUrl', [
                'apiUrl' => $apiUrl,
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                 // Log the data being apiResponse
                Log::info('DCR Details API apiResponse', [
                    'apiResponse' => $apiResponse,
                ]);

                if (Arr::get($apiResponse, 'success') && is_array(Arr::get($apiResponse, 'data')) && count(Arr::get($apiResponse, 'data')) > 0) {
                    $dcr = $apiResponse['data'][0];
                    $tourPlanType = Arr::get($dcr, 'extendedProps.tour_plan_type');

                    // Return the appropriate partial view based on tour plan type
                    if ($tourPlanType == 1) {
                        return view('tourplanner.dcrCollectionsPartial', ['dcr' => $dcr]);
                    } elseif ($tourPlanType == 2) {
                        return view('tourplanner.dcrSourcingPartial', ['dcr' => $dcr]);
                    } elseif ($tourPlanType == 3) {
                        return view('tourplanner.dcrBothPartial', ['dcr' => $dcr]);
                    } else {
                        return response()->json(['error' => 'Invalid DCR type received.'], 400);
                    }
                } else {
                    return response()->json(['error' => 'Failed to fetch DCR details.'], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to fetch DCR Details from the external API.'], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching DCR Details from external API.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while fetching DCR Details.'], 500);
        }
    }



    public function updateStatus(Request $request, $id)
    {
        // 1. Validate the incoming request
        $validatedData = $request->validate([
            'status' => 'required|in:approved,rejected,accepted',
            'remarks' => 'nullable|string', // Validate the remarks
        ]);

         // 2. Retrieve the token from the session
         $token = session()->get('api_token');

         if (!$token) {
             Log::warning('API token missing in session.');
             return response()->json([
                 'success' => false,
                 'message' => 'Authentication token missing. Please log in again.'
             ], 401);
         }

            // 3. Define the external API URL for updating DCR status
        $apiUrl = config('auth_api.dcr_update_status_url');

        if (!$apiUrl) {
            Log::error('DCR Update Status URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'DCR Update Status URL is not configured.'
            ], 500);
        }

          // 4. Prepare the payload for the API request
          $payload = [
            'id' => $id,
            'status' => $validatedData['status'],
            'remarks' => $validatedData['remarks'],  // Add remarks to the payload
            'updated_by' => Auth::id(), // Assuming the authenticated user is performing the update
        ];

        // Log the payload being sent
        Log::info('Sending DCR Status Update to External API', [
            'api_url' => $apiUrl,
            'payload' => $payload,
        ]);

        try {
            // Step 7: Make the API call to update the DCR status
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $payload);
    
            // Log the API response
            Log::info('External API Response for DCR Status Update', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
    
            // Step 8: Handle the API response
            if ($response->successful()) {
                $apiResponse = $response->json();
    
                if (Arr::get($apiResponse, 'success')) {
                    return redirect()->back()->with('success', Arr::get($apiResponse, 'message', 'DCR status updated successfully.'));
                } else {
                    Log::warning('External API returned failure for DCR Status Update.', [
                        'message' => Arr::get($apiResponse, 'message'),
                    ]);
                    return redirect()->back()->with('error', Arr::get($apiResponse, 'message', 'Failed to update DCR status.'));
                }
            } else {
                Log::error('Failed to update DCR status via external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return redirect()->back()->with('error', 'Failed to update DCR status via the external API.');
            }
        } catch (\Exception $e) {
            // Handle exceptions, such as network issues
            Log::error('Exception while updating DCR status via external API.', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while updating the DCR status.');
        }
    }


    /**
     * Show the  tour plan FINAL DCR Requests list page.
     *
     * @return \Illuminate\View\View
     */
    public function finalDCRRequests()
    {
        return view('tourplanner.finalDCR');
    }


     /**
     * API Endpoint to Fetch DInal DCRApprovals based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFinalDCRApprovals(Request $request)
    {
        // Retrieve filters from the request
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

        // Define the external API URL for fetching calendar events
        $apiUrl = config('auth_api.final_dcr_approvals_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Final DCR Approvals fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Final DCR Approvals fetch URL is not configured.'
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
            Log::info('Final Fetch DCR Approvals request API', [
                'data' => $queryParams,
            ]);


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response for Final DCR Approvals', [
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
                    Log::warning('External API returned failure for Final DCR Approvals.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch Final DCR Approvals from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Final DCR Approvals from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Final DCR Approvals from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Final DCR Approvals.',
            ], 500);
        }
    }


    /**
     * Display the specified DCR final visit details by fetching data from an external API.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showFinalDCRVisitDetails(Request $request, $id)
    {
        // 1. Validate the input
        if (!$id || !is_numeric($id)) {
            return redirect()->back()->with('error', 'DCR Visit ID is required and must be a valid number.');
        }

        // Retrieve the additional query parameters
        $empId = $request->query('emp_id');
        $visitDate = $request->query('visit_date');

        // Log the additional parameters if needed
        Log::info('DCR Visit Details Params', [
            'id' => $id,
            'emp_id' => $empId,
            'visit_date' => $visitDate,
        ]);

        // Redirect to the dcrVisits route with the query parameters
        return redirect()->route('tourplanner.dcrVisits', [
            'id' => $id,
            'emp_id' => $empId,
            'visit_date' => $visitDate,
        ]);

        // // 2. Retrieve the token from the session
        // $token = session()->get('api_token');

        // if (!$token) {
        //     Log::warning('API token missing in session.');
        //     return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
        // }


        // // 3. Define the external API URL for fetching DCR details
        // $apiUrl = config('auth_api.final_dcr_visit_details_fetch_all_url');

        // if (!$apiUrl) {
        //     Log::error('DCR details fetch URL not configured.');
        //     return redirect()->back()->with('error', 'DCR details fetch URL is not configured.');
        // }

        // try {
        //     // 4. Build query parameters
        //     $queryParams = [
        //         'id' => $id,
        //         'emp_id' => $empId,
        //         'visit_date' => $visitDate,
        //     ];

        //     // 5. Log the request data
        //     Log::info('Fetch DCR Details request API', [
        //         'data' => $queryParams,
        //     ]);

        //     // 6. Make the API call
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $token,
        //         'Accept' => 'application/json',
        //     ])->get($apiUrl, $queryParams);

        //     // 7. Log the API response
        //     Log::info('External API Response for DCR Details', [
        //         'status' => $response->status(),
        //         'body' => $response->body(),
        //     ]);

        //     // 8. Handle successful API response
        //     if ($response->successful()) {
        //         $apiResponse = $response->json();

        //         if (Arr::get($apiResponse, 'success') && is_array(Arr::get($apiResponse, 'data')) && count(Arr::get($apiResponse, 'data')) > 0) {
        //             // Assuming the API returns an array with at least one DCR detail
        //             $dcr = $apiResponse['data'][0];
    
        //             // Instead of determining tour plan type and redirecting to separate views,
        //             // return a single view (tourplanner.dcrVisits) with the complete DCR details.
        //             return view('tourplanner.dcrVisits', ['dcr' => $dcr]);
        //         } else {
        //             // API returned success: false or no data
        //             $message = Arr::get($apiResponse, 'message', 'Failed to fetch DCR details.');
        //             Log::warning('External API returned failure for DCR Details.', ['message' => $message]);
        //             return redirect()->back()->with('error', $message);
        //         }
        //     } else {
        //         $status = $response->status();
        //     $error = $response->body();

        //     Log::error('Failed to fetch DCR Details from external API.', [
        //         'status' => $status,
        //         'body' => $error,
        //     ]);

        //     return redirect()->back()->with('error', 'Failed to fetch DCR Details from the external API.');
        //     }
        // } catch (\Exception $e) {
        //     // Handle exceptions, such as network issues
        //     Log::error('Exception while fetching DCR Details from external API.', ['error' => $e->getMessage()]);
        //     return redirect()->back()->with('error', 'An error occurred while fetching DCR Details.');
        // }
    }

    public function dcrVisits(Request $request)
    {
        // Retrieve the query parameters
        $id = $request->query('id');
        $empId = $request->query('emp_id');
        $visitDate = $request->query('visit_date');

        // Log received parameters
        Log::info('dcrVisits called with params', [
            'id' => $id,
            'emp_id' => $empId,
            'visit_date' => $visitDate,
        ]);

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
        }

        // Define your external API URL for fetching the details
        $apiUrl = config('auth_api.final_dcr_visit_details_fetch_all_url');
        if (!$apiUrl) {
            return redirect()->back()->with('error', 'DCR details fetch URL is not configured.');
        }

        try {
            // Build the query parameters for the API call
            $queryParams = [
                'id' => $id,
                'emp_id' => $empId,
                'visit_date' => $visitDate,
            ];

            // Make the API call
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success') && !empty(Arr::get($apiResponse, 'data'))) {
                    // Here we assume the API returns a JSON with "success" and "data" keys
                    $dcrData = $apiResponse;
                } else {
                    return redirect()->back()->with('error', 'Failed to fetch DCR Details from the external API.');
                }
            } else {
                return redirect()->back()->with('error', 'Failed to fetch DCR Details from the external API.');
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching DCR Details', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while fetching DCR Details.');
       }

        // Pass the full data array (as received from the API) to the view
        return view('tourplanner.dcrVisits', ['dcrData' => $dcrData]);
    }


    public function dcrStatusFetch(Request $request)
    {
        // Retrieve the query parameters
        $id = $request->query('id');
        $empId = $request->query('emp_id');
        $visitDate = $request->query('visit_date');

        // Log received parameters
        Log::info('dcrStatusFetch called with params', [
            'id' => $id,
            'emp_id' => $empId,
            'visit_date' => $visitDate,
        ]);

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define your external API URL for fetching the details
        $apiUrl = config('auth_api.final_dcr_status_fetch_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'DCR details fetch URL is not configured.'
            ], 500);
        }

        try {
            // Build the query parameters for the API call
            $queryParams = [
                'id' => $id,
                'emp_id' => $empId,
                'visit_date' => $visitDate,
            ];

            // Make the API call
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success') && !empty(Arr::get($apiResponse, 'data'))) {
                    // Return the entire API response as JSON
                    return response()->json($apiResponse);
                } else {
                    Log::warning('External API returned failure for DCR Status fetch.', [
                        'message' => Arr::get($apiResponse, 'message')
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Failed to fetch DCR status.')
                    ], 400);
                }
            } else {
                Log::error('Failed to fetch DCR status via external API.', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch DCR status via the external API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching DCR Details', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching DCR Details.'
            ], 500);
        }
    }


    /**
     * API Endpoint to Fetch Employee  City Mapped Blood Banks Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getEmployeesBloodBanks(Request $request)
    {
        // Retrieve the auth user id from the query parameter (if needed)
        $employeeId = $request->input('auth_user_id');
        Log::info('Received auth_user_id for employee blood banks fetch', ['employeeId' => $employeeId]);

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
        $apiUrl = config('auth_api.employee_blood_bank_fetch_url');

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
            ])->get($apiUrl, ['employeeId' => $employeeId]); // Pass auth_user_id to external API

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


    public function getEmployeeCities(Request $request)
    {

        // Retrieve the auth user id from the query parameter (if needed)
        $employeeId = $request->input('agent_id');
        Log::info('Received auth_user_id for employee cities fetch', ['employeeId' => $employeeId]);


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
        $apiUrl = config('auth_api.employee_cities_fetch_url');

        if (!$apiUrl) {
            Log::error('Users fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Users fetch URL is not configured.'
            ], 500);
        }

      //  Log::info('Fetching users from external API.', ['api_url' => $apiUrl]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['employeeId' => $employeeId]); // Pass auth_user_id to external API

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
                Log::error('Failed to fetch users from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch users from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching users from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users.',
            ], 500);
        }
    }


    /**
     * Show the tour plan list page.
     *
     * @return \Illuminate\View\View
    */
    public function showSourcingCreateTourPlan()
    {
        return view('tourplanner.sourcingCreateTourPlan');
    }


    public function submitMonthlyTourPlan(Request $request)
    {
        $employeeId = $request->input('agent_id');
        $month      = $request->input('month');
        $status     = $request->input('status');

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token missing. Please log in again.'
                ], 401);
            }
            return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
        }

        // Define your external API URL for submission
        $apiUrl = config('auth_api.tour_plan_monthly_submit_url');
        if (!$apiUrl) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Monthly submission URL is not configured.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Monthly submission URL is not configured.');
        }

        try {
            // Build the POST data for the API call
            $postData = [
                'employeeId' => $employeeId,
                'month'      => $month,
                'status'     => $status,
            ];

            Log::info('Received employee monthly TP Submit postData', ['postData' => $postData]);

            // Make the API call using POST
            $apiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post($apiUrl, $postData);

            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                Log::info('Received employee monthly TP Submit apiData', ['apiData' => $apiData]);

                if (Arr::get($apiData, 'success')) {
                    // For AJAX, return JSON response
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Monthly tour plan submitted successfully.',
                            'data' => $apiData['data'] // e.g., updated_rows, new_status
                        ]);
                    }
                    // Otherwise, redirect
                    return redirect()->route('tourplanner.sourcingCreateTourPlan')
                        ->with('success', 'Monthly tour plan submitted successfully.');
                } else {
                    Log::warning('External API returned failure on monthly tour plan submission.', [
                        'message' => Arr::get($apiData, 'message')
                    ]);
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => Arr::get($apiData, 'message', 'Failed to submit monthly tour plan.')
                        ], 400);
                    }
                    return redirect()->back()->with('error', Arr::get($apiData, 'message', 'Failed to submit monthly tour plan.'));
                }
            } else {
                Log::error('Failed to submit monthly tour plan. API response:', [
                    'status' => $apiResponse->status(),
                    'body'   => $apiResponse->body(),
                ]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to submit monthly tour plan due to an API error.'
                    ], $apiResponse->status());
                }
                return redirect()->back()->with('error', 'Failed to submit monthly tour plan due to an API error.');
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit monthly tour plan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to submit monthly tour plan: ' . $e->getMessage());
        }
    }


    public function submitEditRequest(Request $request)
    {
        $agentId = $request->input('agent_id');
        $month   = $request->input('month');
        $editRequest = $request->input('edit_request'); // Should be 1
        $editRequestReason = $request->input('edit_request_reason');

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token missing. Please log in again.'
                ], 401);
            }
            return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
        }

        // Define your external API URL for edit request submission
        $apiUrl = config('auth_api.tour_plan_edit_request_url');
        if (!$apiUrl) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Edit request submission URL is not configured.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Edit request submission URL is not configured.');
        }

        try {
            // Build the POST data for the API call
            $postData = [
                'employeeId' => $agentId,
                'month'      => $month,
                'edit_request' => $editRequest, // expected to be 1
                'edit_request_reason' => $editRequestReason,
            ];

            Log::info('Received employee edit request postData', ['postData' => $postData]);

            // Make the API call using POST
            $apiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post($apiUrl, $postData);

            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                Log::info('Received employee edit request apiData', ['apiData' => $apiData]);

                if (Arr::get($apiData, 'success')) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Edit request submitted successfully.',
                            'data' => $apiData['data'] // e.g., updated_rows, etc.
                        ]);
                    }
                    return redirect()->route('tourplanner.sourcingCreateTourPlan')
                        ->with('success', 'Edit request submitted successfully.');
                } else {
                    Log::warning('External API returned failure on edit request submission.', [
                        'message' => Arr::get($apiData, 'message')
                    ]);
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => Arr::get($apiData, 'message', 'Failed to submit edit request.')
                        ], 400);
                    }
                    return redirect()->back()->with('error', Arr::get($apiData, 'message', 'Failed to submit edit request.'));
                }
            } else {
                Log::error('Failed to submit edit request. API response:', [
                    'status' => $apiResponse->status(),
                    'body'   => $apiResponse->body(),
                ]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to submit edit request due to an API error.'
                    ], $apiResponse->status());
                }
                return redirect()->back()->with('error', 'Failed to submit edit request due to an API error.');
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit edit request: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to submit edit request: ' . $e->getMessage());
        }
    }


    public function requestCollection(Request $request)
    {
        $tourPlanId = $request->input('tour_plan_id');
        $requestSameCity = $request->input('request_same_city'); // Expected to be 0 or 1
        $message = $request->input('message');

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token missing. Please log in again.'
                ], 401);
            }
            return redirect()->back()->with('error', 'Authentication token missing. Please log in again.');
        }

        // Define your external API URL for collection request submission
        $apiUrl = config('auth_api.tour_plan_collection_request_url');
        if (!$apiUrl) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Collection request URL is not configured.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Collection request URL is not configured.');
        }

        try {
            // Build the POST data for the API call
            $postData = [
                'tour_plan_id'       => $tourPlanId,
                'request_same_city'  => $requestSameCity, // expected to be 0 or 1
                'message'            => $message,
            ];

            Log::info('Received collection request postData', ['postData' => $postData]);

            // Make the API call using POST
            $apiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->post($apiUrl, $postData);

            if ($apiResponse->successful()) {
                $apiData = $apiResponse->json();
                Log::info('Received collection request apiData', ['apiData' => $apiData]);

                if (Arr::get($apiData, 'success')) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Collection request submitted successfully.',
                            'data'    => $apiData['data'] // e.g., updated_rows, etc.
                        ]);
                    }
                    return redirect()->route('tourplanner.sourcingCreateTourPlan')
                        ->with('success', 'Collection request submitted successfully.');
                } else {
                    Log::warning('External API returned failure on collection request submission.', [
                        'message' => Arr::get($apiData, 'message')
                    ]);
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => Arr::get($apiData, 'message', 'Failed to submit collection request.')
                        ], 400);
                    }
                    return redirect()->back()->with('error', Arr::get($apiData, 'message', 'Failed to submit collection request.'));
                }
            } else {
                Log::error('Failed to submit collection request. API response:', [
                    'status' => $apiResponse->status(),
                    'body'   => $apiResponse->body(),
                ]);
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to submit collection request due to an API error.'
                    ], $apiResponse->status());
                }
                return redirect()->back()->with('error', 'Failed to submit collection request due to an API error.');
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit collection request: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to submit collection request: ' . $e->getMessage());
        }
    }


    /**
     * Show the tour plan Collection Reuest from Sourcing Agentslist page.
     *
     * @return \Illuminate\View\View
    */
    public function showCollectionIncomingRequests()
    {
        return view('tourplanner.collectionIncomingRequests');
    }

    
    /**
     * API Endpoint to Fetch TP Collection Requests.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getTPCollectionIncomingRequests()
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
        $apiUrl = config('auth_api.tour_plan_collection_fetch_url');

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
     * Mark TP as added by calling the external API.
     * Expects a GET parameter 'tp_id'.
     */
    public function markTPAdded(Request $request)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session for markTPAdded.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Validate that tp_id is provided
        $tp_id = $request->query('tp_id');
        if (!$tp_id) {
            return response()->json([
                'success' => false,
                'message' => 'TP id is required.'
            ], 400);
        }

        // Define the external API URL for marking TP as added.
        // Make sure this is set in your configuration, e.g., config/auth_api.php
        $apiUrl = config('auth_api.mark_tp_added_url');

        if (!$apiUrl) {
            Log::error('Mark TP Added URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Mark TP Added URL is not configured.'
            ], 500);
        }

        Log::info('Marking TP as added via external API.', ['api_url' => $apiUrl, 'tp_id' => $tp_id]);

        try {
            // Call the external API with the tp_id as parameter (GET request in this example)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl, ['tp_id' => $tp_id]);

            Log::info('External API Response for markTPAdded', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'message' => Arr::get($apiResponse, 'message', 'TP marked as added successfully.')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Failed to mark TP as added.')
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark TP as added from the external API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while marking TP as added from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking TP as added.'
            ], 500);
        }
    }

    /**
     * API Endpoint to Fetch All Active Warehouses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllActiveWarehouses()
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
        $apiUrl = config('auth_api.warehouse_fetch_all_active_url');

        if (!$apiUrl) {
            Log::error('Warehouse Active fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Warehouse Active fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching Active Warehouse from external API.', ['api_url' => $apiUrl]);

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
                Log::error('Failed to fetch Warehouse from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Active Warehouse from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Active Warehouse from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Active Warehouse.',
            ], 500);
        }
    }


     /**
     * API Endpoint to Fetch All Active Transport Partners.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllActiveTransportPartners()
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
        $apiUrl = config('auth_api.transport_partners_fetch_all_active_url');

        if (!$apiUrl) {
            Log::error('Transport Partners fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Transport Partners fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching Transport Partners from external API.', ['api_url' => $apiUrl]);

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
                Log::error('Failed to fetch Transport Partners from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Transport Partners from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Transport Partners from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Transport Partners.',
            ], 500);
        }
    }


    /**
     * API Endpoint to Update Vehicle Details
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function updateVehicleDetails(Request $request)
    {
        // Validate the incoming data.
        $validatedData = $request->validate([
            'transport_detail_id'    => 'required|integer|exists:transport_details,id',
            'collection_request_id'  => 'required|integer|exists:tour_plan,id',
            'warehouse_id'           => 'required|integer|exists:entities,id',
            'transport_partner_id'   => 'required|integer|exists:transport_partners,id',
            'vehicle_number'         => 'required|string|max:50',
            'driver_name'            => 'required|string|max:100',
            'contact_number'         => 'required|string|max:15',
            'email_id'               => 'required|email|max:255',
            'alternate_mobile_no'    => 'nullable|string|max:15',
            'remarks'                => 'nullable|string|max:255',
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
        $apiUrl = config('auth_api.vehicle_details_update_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('Vehicle Details Submit URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Vehicle Details Submit URL is not configured.'
            ], 500);
        }

         // Step 3: Map form fields to transport_details table fields
         $transportData = [
            'transport_detail_id' => $validatedData['transport_detail_id'],
            'collection_request_id' => $validatedData['collection_request_id'],
            'warehouse_id'          => $validatedData['warehouse_id'],
            'transport_partner_id'  => $validatedData['transport_partner_id'],
            'vehicle_number' => $validatedData['vehicle_number'],
            'driver_name' => $validatedData['driver_name'],
            'contact_number' => $validatedData['contact_number'],
            'alternative_contact_number' => $validatedData['alternate_mobile_no'] ?? null,
            'email_id' => $validatedData['email_id'],
            'remarks' => $validatedData['remarks'] ?? null,
            'created_by' => Auth::id(), // assuming user is authenticated
            'modified_by' => Auth::id(),
        ];

        Log::info('Vehicle Details Update Request external API.', ['transportData' => $transportData]);


        try {
            // Send the data to the external API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $transportData);

            Log::info('External API Response for Vehicle Update Details Submission', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'message' => Arr::get($apiResponse, 'message', 'Vehicle details updated successfully.')
                    ]);
                } else {
                    Log::warning('External API returned failure for Vehicle Details Update Submission.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.')
                    ]);
                }
            } else {
                Log::error('Failed to submit vehicle details Update via external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to Update the vehicle details via the external API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while Update vehicle details via external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while Update vehicle details.'
            ], 500);
        }
    }


    /**
     * API Endpoint to Cancel a Tour Plan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelTourPlan(Request $request)
    {
        // 1) Validate
        $request->validate([
            'tour_plan_id' => 'required|integer',
        ]);

        // 2) Token & URL
        $token  = session('api_token');
        $apiUrl = config('auth_api.tour_plan_cancel_url');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Authentication token missing.'], 401);
        }
        if (!$apiUrl) {
            return response()->json(['success' => false, 'message' => 'Cancel URL not configured.'], 500);
        }

        try {
            $postData = ['tour_plan_id' => $request->input('tour_plan_id')];
            Log::info('Cancel Tour Plan request postData', ['postData' => $postData]);

            // 3) Actually call the API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Accept'        => 'application/json',
            ])->post($apiUrl, $postData);

            // 4) Log the raw HTTP response
            Log::info('External API Response for Cancel Tour Plan', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResult = $response->json();
               
                // business-rule failure: change code to 200
                if (! Arr::get($apiResult, 'success')) {
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResult, 'message', 'Unknown error from API.')
                    ]);  // <-- NO ,400 here
                }

                // success!
                return response()->json([
                    'success' => true,
                    'message' => Arr::get($apiResult, 'message', 'Cancelled successfully.')
                ]);
            }

            // non-2xx
            Log::error('Failed to cancel tour plan via external API.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
           // non-2xx from the external API — you might still want this to be 502 or 500
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel tour plan via the external API.'
            ], 502);

        } catch (\Exception $e) {
            Log::error('Exception while cancel tour plan via external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling the tour plan.'
            ], 500);
        }
    }

    
    /**
     * API Endpoint to Fetch All DCR ApprovalsCollecting Agents Users Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getDCRApprovalsCollectingAgents()
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
        $apiUrl = config('auth_api.dcr_approvals_collecting_agents_fetch_by_managerId_url');

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
     * Show the pending tour plan FINAL DCR Submit list page.
     *
     * @return \Illuminate\View\View
     */
    public function pendingDCRSubmit()
    {
        return view('tourplanner.pendingDCRSubmit');
    }


    
     /**
     * API Endpoint to Fetch Pending DCR Submits based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingDCRSubmits(Request $request)
    {
        // Retrieve filters from the request
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

        // Define the external API URL for fetching calendar events
        $apiUrl = config('auth_api.final_pending_dcr_submit_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Final DCR Approvals fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Final DCR Approvals fetch URL is not configured.'
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
            Log::info('Final Fetch DCR Approvals request API', [
                'data' => $queryParams,
            ]);


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response for Final DCR Approvals', [
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
                    Log::warning('External API returned failure for Final DCR Approvals.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch Final DCR Approvals from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Final DCR Approvals from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Final DCR Approvals from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Final DCR Approvals.',
            ], 500);
        }
    }

}
