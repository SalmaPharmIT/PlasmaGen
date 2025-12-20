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


    /**
     * Show the Bloodbanks Summarylist page.
     *
     * @return \Illuminate\View\View
     */
    public function bloodbankSummaryIndex()
    {

        return view('reports.blood_banks_summary');
    }


    /**
     * Show the BloodBank Summary Data page.
     *
     * @return \Illuminate\View\View
     */
    public function getBloodBankSummaryData(Request $request)
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
        $apiUrl = config('auth_api.reports_blood_banks_summary_url');

        if (!$apiUrl) {
            Log::error('Blood Bank Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Blood Bank Summary fetch URL is not configured.'
            ], 500);
        }

        // Retrieve filter parameters from the request
        $stateIds = $request->input('stateIds'); // State ID from dropdown
        $dateRange = $request->input('dateRange'); // Date range in "YYYY-MM-DD - YYYY-MM-DD" format

        // Prepare the payload to submit to the external API
        $payload = [
            'stateIds'   => $stateIds,
            'dateRange' => $dateRange,
        ];

         // Log the data being sent
         Log::info('getBloodBankSummaryData request API', [
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
                Log::error('Failed to fetch Blood Bank Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Blood Bank Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Blood Bank Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Blood Bank Summary.',
            ], 500);
        }
    }

    /**
     * Show the Bloodbanks Summarylist page.
     *
     * @return \Illuminate\View\View
    */
    public function userWiseCollectionSummaryIndex()
    {

        return view('reports.user_wise_collection_summary');
    }

    public function getUserWiseColllectionSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise collections
        $apiUrl = config('auth_api.reports_user_wise_collection_summary_url');

        if (!$apiUrl) {
            Log::error('User wise collection Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'User wise collection Summary fetch URL is not configured.'
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
         Log::info('getUserWiseColllectionSummaryData request API', [
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
                Log::error('Failed to fetch User wise collection Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch User wise collection Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching User wise collection Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching User wise collection Summary.',
            ], 500);
        }
    }

    /**
     * Show the Bloodbank wise collection Summary page.
     *
     * @return \Illuminate\View\View
    */
    public function bloodBankWiseCollectionSummaryIndex()
    {

        return view('reports.bloodbank_wise_collection_summary');
    }

    public function getBloodBankWiseColllectionSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise collections
        $apiUrl = config('auth_api.reports_blood_bank_wise_collection_summary_url');

        if (!$apiUrl) {
            Log::error('Blood Bank wise collection Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Blood Bank wise collection Summary fetch URL is not configured.'
            ], 500);
        }

        // Retrieve filter parameters from the request
        $bloodBanksIds = $request->input('bloodBanks');
        $statesIds = $request->input('states');
        $citiesIds = $request->input('cities');
        $dateRange = $request->input('dateRange'); // Date range in "YYYY-MM-DD - YYYY-MM-DD" format

        // Prepare the payload to submit to the external API
        $payload = [
            'blood_bank_ids'   => $bloodBanksIds,
            'state_ids'   => $statesIds,
            'city_ids'   => $citiesIds,
            'dateRange' => $dateRange,
        ];

         // Log the data being sent
         Log::info('getBloodBankWiseColllectionSummaryData request API', [
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
                Log::error('Failed to fetch Blood Bank wise collection Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Blood Bank wise collection Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Blood Bank wise collection Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Blood Bank wise collection Summary.',
            ], 500);
        }
    }


    /**
     * Show the tour planner datewise Summary page.
     *
     * @return \Illuminate\View\View
    */
    public function tourPlanDateWiseSummaryIndex()
    {

        return view('reports.tour_palnner_datewise_summary');
    }

    public function getTourPlannerDateWiseSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise collections
        $apiUrl = config('auth_api.reports_tour_palnner_datewise_summary_url');

        if (!$apiUrl) {
            Log::error('Tour Planner Datewise Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Tour Planner Datewise Summary fetch URL is not configured.'
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
         Log::info('getTourPlannerDateWiseSummaryData request API', [
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
                Log::error('Failed to fetch Tour Planner Datewise summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Tour Planner Datewise  Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Tour Planner Datewise  Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Tour Planner Datewise Summary.',
            ], 500);
        }
    }

    /**
     * Show the User wise expenses page.
     *
     * @return \Illuminate\View\View
    */
    public function userExpensesSummaryIndex()
    {

        return view('reports.user_expenses_summary');
    }


    public function getUserExpensesSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise collections
        $apiUrl = config('auth_api.reports_user_expenses_summary_url');

        if (!$apiUrl) {
            Log::error('User wise collection Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'User wise collection Summary fetch URL is not configured.'
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
         Log::info('getUserExpensesSummaryData request API', [
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
                Log::error('Failed to fetch User Expenses Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch User Expenses Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching User Expenses Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching User Expenses Summary.',
            ], 500);
        }
    }

    /**
     * Show the DCR Summary page.
     *
     * @return \Illuminate\View\View
    */
    public function dcrSummaryIndex()
    {

        return view('reports.dcr_summary');
    }

    public function getUserDCRSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise DCR
        $apiUrl = config('auth_api.reports_user_dcr_summary_url');

        if (!$apiUrl) {
            Log::error('User wise DCR Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'User wise DCR Summary fetch URL is not configured.'
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
         Log::info('getUserDCRSummaryData request API', [
            'data' => $payload,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $payload);

            Log::info('External API Response getUserDCRSummaryData', [
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
                Log::error('Failed to fetch User DCR Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch User DCR Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching User DCR Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching User DCR Summary.',
            ], 500);
        }
    }

    /**
     * Show the User Live Location page.
     *
     * @return \Illuminate\View\View
    */
    public function userLiveLocationIndex()
    {

        return view('reports.user_live_location');
    }

    /**
     * Fetch User Live Location Data
     */
    public function getUserLiveLocation(Request $request)
    {
        // Retrieve token
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // External API URL
        $apiUrl = config('auth_api.reports_user_live_location_url');

        if (!$apiUrl) {
            Log::error('Live Location URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Live Location API URL is not configured.'
            ], 500);
        }

        // Inputs from frontend
        $empId = $request->input('empId');
        $date  = $request->input('date');

        $payload = [
            'emp_id' => $empId,
            'date'   => $date,
        ];

        Log::info('User Live Location Request Sent', ['payload' => $payload]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json'
            ])->post($apiUrl, $payload);

            Log::info('User Live Location API Response', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'data'    => Arr::get($apiResponse, 'data', [])
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => Arr::get($apiResponse, 'message', 'Unknown error')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'External API error occurred.'
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Exception in User Live Location', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error occurred while fetching location.'
            ], 500);
        }
    }

    /**
     * Show the DCR Summary with Expenses page.
     *
     * @return \Illuminate\View\View
    */
    public function dcrSummaryExpensesIndex()
    {

        return view('reports.dcr_summary_expenses');
    }

    /**
     * Show the DCR Summary with Expenses Deata page.
     *
     * @return \Illuminate\View\View
    */
    public function getUserDCRWithExpensesSummaryData(Request $request)
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

        // Define the external API URL for fetching User wise DCR
        $apiUrl = config('auth_api.reports_dcr_details_expenses_url');

        if (!$apiUrl) {
            Log::error('User wise DCR with Expenses Summary fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'User wise DCR with Expenses Summary fetch URL is not configured.'
            ], 500);
        }

        // FIX HERE
        $agent_id = $request->input('agent_id'); 
        $dateRange = $request->input('dateRange'); 

        // Prepare the payload to submit to the external API
        $payload = [
            'agent_id'   => $agent_id,
            'dateRange' => $dateRange,
        ];

         // Log the data being sent
         Log::info('getUserDCRWithExpensesSummaryData request API', [
            'data' => $payload,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post($apiUrl, $payload);

            Log::info('External API Response getUserDCRWithExpensesSummaryData', [
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
                Log::error('Failed to fetch User DCR with Expenses Summary from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch User DCR with Expenses Summary from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching User DCR with Expenses Summary from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching User DCR with Expenses Summary.',
            ], 500);
        }
    }

}
