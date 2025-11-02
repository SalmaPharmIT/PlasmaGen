<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PlasmaEntry;
use App\Models\BagEntryDetail;
use App\Models\BagEntryMiniPool;
use App\Models\ElisaTestReport;
use App\Models\NATTestReport;
use App\Models\SubMiniPoolElisaTestReport;
use App\Models\AuditTrail;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Log dashboard access
        AuditTrail::log(
            'view',
            'Dashboard',
            'Main Dashboard',
            null,
            [],
            [],
            'User accessed the dashboard'
        );

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

            // Get total plasma entries quantity in liters
            $totalCollections = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->sum('plasma_qty') ?? 0;

            // Get approved plasma quantity in liters
            $approvedCount = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->whereNotNull('alloted_ar_no')->sum('plasma_qty') ?? 0;

            // Get rejected plasma quantity in liters
            $rejectedCount = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
            })->whereNotNull('destruction_no')->sum('plasma_qty') ?? 0;

            // Get tail cutting plasma quantity in liters
            // bag_volume_ml is in milliliters - convert to liters
            $tailCuttingVolumeMl = BagEntryDetail::when($dateRange, function($query, $dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->where('tail_cutting', 'Yes')
            ->sum('bag_volume_ml');

            $tailCuttingCount = ($tailCuttingVolumeMl / 1000) ?? 0; // Convert ml to liters

            // Get actual dispensed plasma quantity in liters
            // issued_volume in bag_status_details is already in liters
            $dispensedCount = DB::table('bag_status_details')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('date', [$dateRange['start'], $dateRange['end']]);
                })
                ->where('status', 'despense')
                ->where('status_type', 'final') // Only count finalized dispensing
                ->whereNull('deleted_at')
                ->sum('issued_volume') ?? 0;

            // Get ELISA test statistics
            $elisaReactiveCount = DB::table('sub_mini_pool_elisa_test_report')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->where('final_result', 'Reactive')
                ->whereNull('deleted_at')
                ->count();

            $elisaNonReactiveCount = DB::table('sub_mini_pool_elisa_test_report')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->where('final_result', 'Nonreactive')
                ->whereNull('deleted_at')
                ->count();

            // Get total ELISA test liters
            // mini_pool_bag_volume is already stored in liters
            $elisaTestLiters = DB::table('sub_mini_pool_elisa_test_report')
                ->join('sub_mini_pool_entries', 'sub_mini_pool_elisa_test_report.sub_mini_pool_id', '=', 'sub_mini_pool_entries.id')
                ->join('bag_entries_mini_pools', 'sub_mini_pool_entries.mini_pool_number', '=', 'bag_entries_mini_pools.mini_pool_number')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('sub_mini_pool_elisa_test_report.created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->whereNull('sub_mini_pool_elisa_test_report.deleted_at')
                ->whereNull('sub_mini_pool_entries.deleted_at')
                ->sum('bag_entries_mini_pools.mini_pool_bag_volume'); // Already in liters

            // Get NAT test statistics
            $natReactiveCount = DB::table('nat_test_report')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->where(function($query) {
                    $query->where('hiv', 'reactive')
                          ->orWhere('hbv', 'reactive')
                          ->orWhere('hcv', 'reactive');
                })
                ->whereNull('deleted_at')
                ->count();

            $natNonReactiveCount = DB::table('nat_test_report')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->where('hiv', 'nonreactive')
                ->where('hbv', 'nonreactive')
                ->where('hcv', 'nonreactive')
                ->whereNull('deleted_at')
                ->count();

            // Get total NAT test liters
            // mini_pool_bag_volume is already stored in liters
            $natTestLiters = DB::table('nat_test_report')
                ->join('bag_entries_mini_pools', 'nat_test_report.mini_pool_id', '=', 'bag_entries_mini_pools.mini_pool_number')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('nat_test_report.created_at', [$dateRange['start'], $dateRange['end']]);
                })
                ->whereNull('nat_test_report.deleted_at')
                ->sum('bag_entries_mini_pools.mini_pool_bag_volume'); // Already in liters

            // Calculate pending actions
            // Pending tail cutting: Plasma entries that don't have bag entries created yet
            $pendingTailCutting = DB::table('plasma_entries')
                ->when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('bag_entries')
                        ->whereColumn('bag_entries.grn_no', 'plasma_entries.grn_no')
                        ->whereNull('bag_entries.deleted_at');
                })
                ->where('reciept_date', '<', now()->subHours(24)->format('Y-m-d H:i:s'))
                ->whereNull('plasma_entries.deleted_at')
                ->count();

            // Pending release: Plasma entries that passed tests but not yet released (no AR number)
            $pendingRelease = PlasmaEntry::when($dateRange, function($query, $dateRange) {
                    return $query->whereBetween('reciept_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->whereNull('alloted_ar_no')
                ->whereNull('destruction_no')
                ->count();

            // Pending test entry: Mini pools without test results
            $pendingTestEntry = DB::table('bag_entries_mini_pools')
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                        ->from('sub_mini_pool_elisa_test_report')
                        ->whereColumn('sub_mini_pool_elisa_test_report.mini_pool_number', 'bag_entries_mini_pools.mini_pool_number')
                        ->whereNull('sub_mini_pool_elisa_test_report.deleted_at');
                })
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_collections' => round($totalCollections, 2),
                    'tail_cutting_count' => round($tailCuttingCount, 2),
                    'approved_count' => round($approvedCount, 2),
                    'rejected_count' => round($rejectedCount, 2),
                    'dispensed_count' => round($dispensedCount, 2), // Actual dispensed volume from bag_status_details
                    'elisa_reactive_count' => $elisaReactiveCount,
                    'elisa_non_reactive_count' => $elisaNonReactiveCount,
                    'elisa_test_liters' => round($elisaTestLiters, 2),
                    'nat_reactive_count' => $natReactiveCount,
                    'nat_non_reactive_count' => $natNonReactiveCount,
                    'nat_test_liters' => round($natTestLiters, 2),
                    // Pending actions
                    'pending_tail_cutting' => $pendingTailCutting,
                    'pending_release' => $pendingRelease,
                    'pending_test_entry' => $pendingTestEntry
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
                    'start' => $now->copy()->startOfMonth()->format('Y-m-d'),
                    'end' => $now->copy()->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 3 Months':
                return [
                    'start' => $now->copy()->subMonths(2)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->copy()->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 6 Months':
                return [
                    'start' => $now->copy()->subMonths(5)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->copy()->endOfMonth()->format('Y-m-d')
                ];
            case 'Last 12 Months':
                return [
                    'start' => $now->copy()->subMonths(11)->startOfMonth()->format('Y-m-d'),
                    'end' => $now->copy()->endOfMonth()->format('Y-m-d')
                ];
            case 'All':
                return null;
            default:
                return [
                    'start' => $now->copy()->startOfMonth()->format('Y-m-d'),
                    'end' => $now->copy()->endOfMonth()->format('Y-m-d')
                ];
        }
    }
}
