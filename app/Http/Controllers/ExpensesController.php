<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class ExpensesController extends Controller
{
   
    /**
     * Show the expenses list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('expenses.index');
    }

    /**
     * API Endpoint to Fetch Updated Visits details based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdatedVisits(Request $request)
    {
        // Retrieve filters from the request
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
        $apiUrl = config('auth_api.visits_updated_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Updated Visits fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Updated Visits fetch URL is not configured.'
            ], 500);
        }

        try {
            // Build query parameters
            $queryParams = [
                'month' => $month,
            ];


            // Log the data being sent
            Log::info('Updated Visits request API', [
                'data' => $queryParams,
            ]);


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response for Updated Visits', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'events' => Arr::get($apiResponse, 'data', []),
                    ]);
                } else {
                    Log::warning('External API returned failure for Updated Visits.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch Updated Visits from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch updated visits from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Updated Visits from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching updated visits.',
            ], 500);
        }
    }

    public function showExpenseView(Request $request)
    {
        // Get date and tp_id from the query string
        $visitDate = $request->query('date');  // Fetch date from the query string
        $tpId = $request->query('tp_id');      // Fetch tp_id from the query string
        
        // Pass them to the view
        return view('expenses.view', compact('visitDate', 'tpId'));
    }

     /**
     * API Endpoint to Fetch Updated Visits details based on filters.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchVisitsExpenses(Request $request)
    {

        // Log the parameters for debugging purposes
        Log::info('Inside fetchVisitsExpenses', [$request->input('visit_date')]);

        // Retrieve filters from the request
        $visit_date = $request->input('date');
        $tp_id = $request->input('tp_id');

        // Validate inputs
        if (!$visit_date) {
            return response()->json([
                'success' => false,
                'message' => 'Visit Date is required.'
            ], 400);
        }

        // Validate inputs
        if (!$tp_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tour Plan ID is required.'
            ], 400);
        }

        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json(['error' => 'Authentication token missing. Please log in again.'], 401);
        }

        // Define the external API URL for fetching DCR details
        $apiUrl = config('auth_api.tour_plan_visits_details_url');
        if (!$apiUrl) {
            return response()->json(['error' => 'Tour Plan visits details fetch URL is not configured.'], 500);
        }

        try {
            // Use the 'tp_id' and 'date' in your API call
            // Construct the query parameters
            $queryParams = [
                'id' => $tp_id,        // Tour Plan ID
                'date' => $visit_date  // Visit Date
            ];

            // Log the parameters for debugging purposes
            Log::info('Sending API request with query parameters:', $queryParams);

            // Make the GET request to the external API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            // Check if the response is successful
            if ($response->successful()) {
                $apiResponse = $response->json();

                // If the response contains success, return the events
                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'events' => Arr::get($apiResponse, 'data', []),
                    ]);
                } else {
                    Log::warning('External API returned failure for tour plan visits details.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                // Log the error if the external API request fails
                Log::error('Failed to fetch tour plan visits details from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch tour plan visits details from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
          // Catch any exceptions and log the error
          Log::error('Exception while fetching tour plan visits details from external API.', ['error' => $e->getMessage()]);
          return response()->json(['error' => 'An error occurred while fetching tour plan visits details.'], 500);
       }
    }

    /**
     * Submit teh expenses form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function submitExpenses(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'foodPrice' => 'nullable|numeric|min:0',
            'conventionPrice' => 'nullable|numeric|min:0',
            'TelFaxPrice' => 'nullable|numeric|min:0',
            'lodgingPrice' => 'nullable|numeric|min:0',
            'sundryPrice' => 'nullable|numeric|min:0',
            'totalPrice' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:255',
            // New validation rules for documents
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,csv,txt|max:2048',
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL
        $apiUrl = config('auth_api.expenses_add_url');

        // Prepare the data to send
        $postData = [
            'date'                => $request->input('date'),
            'description'         => $request->input('description'),
            'foodPrice'           => $request->input('foodPrice'),
            'conventionPrice'     => $request->input('conventionPrice'),
            'TelFaxPrice'         => $request->input('TelFaxPrice'),
            'lodgingPrice'        => $request->input('lodgingPrice'),
            'sundryPrice'         => $request->input('sundryPrice'),
            'totalPrice'          => $request->input('totalPrice'),
            'remarks'          => $request->input('remarks'),
            'modified_by'         => auth()->id(),
            'tour_plan_id'         => $request->input('tour_plan_id'),
        ];

        // Send the request using Laravel's HTTP client
        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ]);

        // Handle multiple documents
        if ($request->hasFile('documents')) {
            $files = $request->file('documents');
            Log::info('Number of files uploaded:', ['count' => count($files)]);
            $postData['documents'] = []; // Initialize as an array
            foreach ($request->file('documents') as $document) {
               
                if ($document->isValid()) {
                    $documentContent = file_get_contents($document->getRealPath());
                    $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                    
                     // Log each file to make sure they're processed
                    Log::info('Processed document:', ['file' => $document->getClientOriginalName()]);
                    $postData['documents'][] = $documentBase64;
                } else {
                    Log::warning('Invalid document file:', ['file' => $document->getClientOriginalName()]);
                }
            }

            Log::info('Processed documents:', ['documents' => $postData['documents']]);
            Log::info('Number of files uploaded:', ['count' => count($request->file('documents'))]);
        } else {
            Log::info('No documents to process.');
        }

        // Log the data being sent
        Log::info('Sending data to Expenses Add API', ['data' => $postData]);

        try {

            $response = $http->post($apiUrl, $postData);

             // Log the API response
             Log::info('Add expense API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);


            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                
                    return back()->with('success', 'Expense added successfully.');
                } else {
                    return back()->withErrors(['update_error' => $apiResponse['message']])->withInput();
                }
            } else {
                return back()->withErrors(['api_error' => 'Failed to connect to the update server.'])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Add Expense API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return back()->withErrors(['exception' => 'An error occurred while adding the expense: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Fetch Expenses based on TP ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function fetchExpenses($tp_id)
    {

        $token = session()->get('api_token');
        if (!$token) {
            return response()->json(['error' => 'Authentication token missing. Please log in again.'], 401);
        }

        // Define the external API URL for fetching expenses details
        $apiUrl = config('auth_api.expenses_fetch_url');
        if (!$apiUrl) {
            return response()->json(['error' => 'Tour Plan Expenses fetch URL is not configured.'], 500);
        }

        try {
            // Log the parameters for debugging purposes
            Log::info('Fetching expenses for Tour Plan ID:', ['tp_id' => $tp_id]);

            // Make the GET request to the external API to fetch expenses
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['id' => $tp_id]);

            // Check if the response is successful
            if ($response->successful()) {
                $apiResponse = $response->json();

                // If the response contains success, return the events (expenses)
                if (Arr::get($apiResponse, 'success')) {
                    $events = Arr::get($apiResponse, 'data', []);

                    return response()->json([
                        'success' => true,
                        'data' => $events,  // Return the events directly without adding documents
                    ]);
                } else {
                    // Log the API failure
                    Log::warning('External API returned failure for tour plan expenses.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                // Log the error if the external API request fails
                Log::error('Failed to fetch expenses from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch expenses from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Catch any exceptions and log the error
            Log::error('Exception while fetching expenses from external API.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while fetching expenses.'], 500);
        }

        // Fetch local expenses if external API fails
    }


     /**
     * Delete Expenses based on Expense ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function deleteExpense($id)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            return response()->json(['error' => 'Authentication token missing. Please log in again.'], 401);
        }

        // Define the external API URL for deleting the expense
        $apiUrl = config('auth_api.expenses_delete_url');
        if (!$apiUrl) {
            return response()->json(['error' => 'Expense delete URL is not configured.'], 500);
        }

        try {
            // Send the DELETE request to the external API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->delete($apiUrl, ['id' => $id]);  // Pass the ID here

            // Check if the response is successful
            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false, 'message' => $apiResponse['message']]);
                }
            } else {
                return response()->json(['error' => 'Failed to delete the expense from the external API.'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the expense: ' . $e->getMessage()], 500);
        }
    }



    
}