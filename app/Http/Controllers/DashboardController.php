<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 
use App\Models\PlasmaEntry;
use App\Models\BagEntryDetail;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // Retrieve the authenticated user

        // Check if user has role_id 12 (factory user)
        if ($user && $user->role_id == 12) {
            return view('include.factory-dashboard', compact('user'));
        }

        return view('dashboard', compact('user'));
    }

     /**
     * API Endpoint to Fetch All getDashboardData.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getDashboardData(Request $request)
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

        // Retrieve filter value from GET, defaulting to "This Month"
        $filter = $request->input('filter', 'This Month');

        // Define the external API URL for fetching dashbaord data
        $apiUrl = config('auth_api.dashbaord_web_url');

        if (!$apiUrl) {
            Log::error('Dashboard Data fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Dashboard Data fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['filter' => $filter]);

            Log::info('External API Response getDashboardData', [
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
                Log::error('Failed to fetch dashboard data from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch dashboard data from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching dashboard data from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data.',
            ], 500);
        }
    }

    public function getDashboardGraphData(Request $request)
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

        // Retrieve filter value from GET, defaulting to "This Month"
        $filter = $request->input('filter', 'This Month');

        // Define the external API URL for fetching monthly dashboard graph data
        $apiUrl = config('auth_api.dashboard_web_by_month_url'); // Ensure you configure this in config/auth_api.php

        if (!$apiUrl) {
            Log::error('Dashboard Graph Data fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Dashboard Graph Data fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['filter' => $filter]);

            Log::info('External API Graph Data Response', [
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
                Log::error('Failed to fetch dashboard graph data from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch dashboard graph data from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching dashboard graph data from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard graph data.',
            ], 500);
        }
    }


    public function getDashboardBloodBanksMapData(Request $request)
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

        // Retrieve filter value from GET, defaulting to "This Month"
        $filter = $request->input('filter', 'This Month');

        // Define the external API URL for fetching monthly dashboard graph data
        $apiUrl = config('auth_api.dashboard_web_bloodbanks_map_url'); // Ensure you configure this in config/auth_api.php

        if (!$apiUrl) {
            Log::error('Dashboard BloodBank Map Data fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Dashboard BloodBank Map Data fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['filter' => $filter]);

            Log::info('External API BloodBank Map Data Response', [
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
                Log::error('Failed to fetch dashboard BloodBank Map data from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch dashboard BloodBank Map data from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching dashboard BloodBank Map data from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching dashboard BloodBank Map data.',
            ], 500);
        }
    }

    /**
     * Get dashboard data for factory user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFactoryDashboardData(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Only proceed if user is a factory user
            if ($user->role_id != 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $filter = $request->get('filter', 'This Month');
            $dateRange = $this->getDateRangeForFactory($filter);
            
            // Get total plasma entries count
            $totalCollections = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->count();

            // Get approved plasma count
            $approvedCount = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->whereNotNull('alloted_ar_no')->count();

            // Get rejected plasma count
            $rejectedCount = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->whereNotNull('destruction_no')->count();

             // Get tail cutting plasma count
             $tailCuttingCount = BagEntryDetail::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->where('tail_cutting', 'Yes')
            ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_collections' => $totalCollections,
                    'tail_cutting_count' => $tailCuttingCount,
                    'approved_count' => $approvedCount,
                    'rejected_count' => $rejectedCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in factory dashboard data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching factory dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get date range for factory dashboard filters
     *
     * @param string $filter
     * @return array|null
     */
    private function getDateRangeForFactory($filter)
    {
        $now = now();
        
        switch($filter) {
            case 'This Month':
                return [
                    'start' => $now->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 3 Months':
                return [
                    'start' => $now->subMonths(3)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 6 Months':
                return [
                    'start' => $now->subMonths(6)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 12 Months':
                return [
                    'start' => $now->subMonths(12)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d')
                ];
            case 'All':
                return null;
            default:
                return [
                    'start' => $now->startOfMonth()->format('Y-m-d'),
                    'end' => $now->endOfMonth()->format('Y-m-d')
                ];
        }
    }
}
