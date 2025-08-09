<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\BagEntry;
use App\Models\BagEntryDetail;
use App\Models\BagEntryMiniPool;
use App\Models\User;
use App\Models\ElisaTestReport;
use App\Models\NatTestReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportMiniPoolMegaPoolController extends Controller
{
    public function index()
    {
        // Get blood centers from the entities table where entity_type_id is 2 (Blood Bank)
        $bloodCenters = Entity::with(['city', 'state'])
            ->where('entity_type_id', 2)
            ->where('account_status', 'active')
            ->select('id', 'name', 'city_id', 'state_id')
            ->orderBy('name')
            ->get()
            ->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'text' => $bank->name . ' (' . optional($bank->city)->name . ', ' . optional($bank->state)->name . ')'
                ];
            });

        return view('factory.generate_report.mega_pool_mini_pool', compact('bloodCenters'));
    }

    public function fetchMegaPoolData(Request $request)
    {
        Log::info('fetchMegaPoolData Data Response', []);
        try {
            $bagEntries = BagEntry::with([
                'details' => function($query) {
                    $query->where('tail_cutting', 'Yes');
                },
                'miniPools',
                'createdBy',
                'bloodBank'
            ])
            ->where('ar_no', $request->ar_number)
            ->get();

            if ($bagEntries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected AR number'
                ]);
            }

            $allData = [];
            $totalVolume = 0;
            $rowNumber = 1;

            foreach ($bagEntries as $bagEntry) {
                $data = [
                    'header' => [
                        'blood_centre' => $bagEntry->bloodBank->name ?? '-',
                        'date' => $bagEntry->date ? date('d/m/Y', strtotime($bagEntry->date)) : '-',
                        'pickup_date' => $bagEntry->pickup_date ? date('d/m/Y', strtotime($bagEntry->pickup_date)) : '-',
                        'work_station_no' => $bagEntry->work_station ?? '-',
                        'ar_no' => $bagEntry->ar_no ?? '-',
                        'grn_no' => $bagEntry->grn_no ?? '-',
                        'mega_pool' => $bagEntry->mega_pool_no ?? '-'
                    ],
                    'details' => []
                ];

                // Group details by mini pool
                $miniPoolGroups = collect($bagEntry->miniPools)->groupBy('mini_pool_number');

                foreach ($miniPoolGroups as $miniPoolNumber => $miniPools) {
                    $miniPool = $miniPools->first();
                    $miniPoolVolume = $miniPool->mini_pool_bag_volume ?? 0;
                    $totalVolume += floatval($miniPoolVolume);

                    $detailIds = is_array($miniPool->bag_entries_detail_ids)
                        ? $miniPool->bag_entries_detail_ids
                        : json_decode($miniPool->bag_entries_detail_ids, true);

                    $details = $bagEntry->details->whereIn('id', $detailIds);

                    foreach ($details as $index => $detail) {
                        $data['details'][] = [
                            'row_number' => $rowNumber++,
                            'bags_in_mini_pool' => $index + 1,
                            'donor_id' => $detail->donor_id ?? '-',
                            'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                            'blood_group' => $detail->blood_group ?? '-',
                            'bag_volume_ml' => $detail->bag_volume_ml ?? '-',
                            'mini_pool_bag_volume' => number_format(floatval($miniPool->mini_pool_bag_volume), 2),
                            'mini_pool_number' => $miniPoolNumber,
                            'mega_pool_no' => $bagEntry->mega_pool_no ?? '-',
                            'tail_cutting' => $detail->tail_cutting ?? '-',
                            'prepared_by' => $bagEntry->createdBy ?
                                $bagEntry->createdBy->name . ' (' . date('d/m/Y', strtotime($bagEntry->created_at)) . ')' : '-',
                            'mini_pool_test_result' => $miniPool->mini_pool_test_result ?? 'NON-REACTIVE',
                            'mega_pool_test_result' => $miniPool->mega_pool_test_result ?? 'NON-REACTIVE'
                        ];
                    }
                }

                $allData[] = $data;
            }

            // Combine all details from all mega pools
            $combinedDetails = collect($allData)->pluck('details')->flatten(1)->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'header' => $allData[0]['header'], // Use first mega pool's header info
                    'details' => $combinedDetails,
                    'total_volume' => number_format($totalVolume, 2)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchMegaPoolData: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReactiveMiniPools()
    {
        try {
            Log::info('Fetching reactive mini pools');

            $miniPools = ElisaTestReport::where('hbv', 'reactive')
                ->select('mini_pool_id')
                ->distinct()
                ->orderBy('mini_pool_id')
                ->get();

            Log::info('Found mini pools:', ['count' => $miniPools->count()]);

            $formattedPools = $miniPools->map(function($pool) {
                return [
                    'id' => $pool->mini_pool_id,
                    'text' => $pool->mini_pool_id
                ];
            });

            Log::info('Formatted mini pools:', ['data' => $formattedPools]);

            return response()->json($formattedPools);
        } catch (\Exception $e) {
            Log::error('Error in getReactiveMiniPools: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMiniPoolData($mini_pool_id)
    {
        try {
            // Get data from bag_entries_mini_pools with joins
            $barcodeEntry = DB::table('bag_entries_mini_pools')
                ->join('bag_entries_details', function($join) {
                    $join->whereRaw('FIND_IN_SET(bag_entries_details.id, bag_entries_mini_pools.bag_entries_detail_ids)');
                })
                ->join('bag_entries', 'bag_entries.id', '=', 'bag_entries_details.bag_entries_id')
                ->where('bag_entries_mini_pools.mini_pool_number', $mini_pool_id)
                ->select([
                    'bag_entries_details.donor_id',
                    'bag_entries_details.donation_date',
                    'bag_entries_details.blood_group',
                    'bag_entries_details.bag_volume_ml',
                    'bag_entries.mega_pool_no',
                    'bag_entries.ar_no',
                    'bag_entries.work_station',
                    'bag_entries.pickup_date',
                    'bag_entries.grn_no'
                ])
                ->first();

            if (!$barcodeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected mini pool'
                ]);
            }

            Log::info('Barcode Entry found:', ['barcode_entry' => $barcodeEntry]);

            // Get data from plasma_entries table with blood bank name
            $plasmaEntry = DB::table('plasma_entries')
                ->join('entities', 'plasma_entries.blood_bank_id', '=', 'entities.id')
                ->where('plasma_entries.alloted_ar_no', $barcodeEntry->ar_no)
                ->select('plasma_entries.*', 'entities.name as blood_bank_name')
                ->first();

            // Get timestamp from elisa_test_report
            $elisaTest = DB::table('elisa_test_report')
                ->where('mini_pool_id', $mini_pool_id)
                ->latest('timestamp')
                ->first();

            // Get the mini pool entry to get bag_entries_detail_ids
            $miniPoolEntry = DB::table('bag_entries_mini_pools')
                ->where('mini_pool_number', $mini_pool_id)
                ->first();

            if (!$miniPoolEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No mini pool entry found'
                ]);
            }

            // Convert bag_entries_detail_ids to array
            $detailIds = is_array($miniPoolEntry->bag_entries_detail_ids)
                ? $miniPoolEntry->bag_entries_detail_ids
                : json_decode($miniPoolEntry->bag_entries_detail_ids, true);

            if (!is_array($detailIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid bag entries detail IDs format'
                ]);
            }

            // Get donor details from bag_entries_details using the specific IDs
            $donorDetails = DB::table('bag_entries_details')
                ->whereIn('id', $detailIds)
                ->select(
                    'no_of_bags',
                    'bags_in_mini_pool',
                    'donor_id',
                    'donation_date',
                    'blood_group',
                    'bag_volume_ml',
                    'tail_cutting'
                )
                ->orderBy('no_of_bags')
                ->get();

            Log::info('Plasma Entry found:', ['plasma_entry' => $plasmaEntry]);
            Log::info('ELISA Test found:', ['elisa_test' => $elisaTest]);
            Log::info('Donor Details found:', ['donor_details' => $donorDetails]);

            // Group donor details into sets of 3 and assign sub-mini pool numbers
            $groupedDetails = [];
            $totalRowCounter = 1; // For continuous numbering 1-12
            $totalVolume = 0; // For calculating total volume
            $donorDetails->chunk(3)->each(function ($chunk, $index) use (&$groupedDetails, $mini_pool_id, $miniPoolEntry, &$totalRowCounter, &$totalVolume) {
                $subMiniPoolNumber = sprintf('%s-%02d', $mini_pool_id, $index + 1);

                // Counter for sub-mini pool (1,2,3)
                $subMiniPoolCounter = 1;
                foreach ($chunk as $detail) {
                    $groupedDetails[] = [
                        'row_number' => $totalRowCounter++, // This will be 1-12
                        'bags_in_mini_pool' => $subMiniPoolCounter++, // This will be 1,2,3 for each group
                        'donor_id' => $detail->donor_id,
                        'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                        'blood_group' => $detail->blood_group,
                        'bag_volume_ml' => $detail->bag_volume_ml,
                        'mini_pool_bag_volume' => $miniPoolEntry->mini_pool_bag_volume,
                        'mini_pool_number' => $mini_pool_id,
                        'sub_mini_pool_number' => $subMiniPoolNumber,
                        'tail_cutting' => $detail->tail_cutting
                    ];
                    // Add to total volume
                    $totalVolume += $detail->bag_volume_ml;
                }
            });

            $data = [
                'success' => true,
                'data' => [
                    'mega_pool_no' => $barcodeEntry->mega_pool_no,
                    'ar_no' => $barcodeEntry->ar_no,
                    'pickup_date' =>  $barcodeEntry->pickup_date,
                    'grn_no' =>  $barcodeEntry->grn_no,
                    'display_date' => $elisaTest ? date('d/m/Y', strtotime($elisaTest->timestamp)) : '-',
                    'blood_bank_name' => $plasmaEntry ? $plasmaEntry->blood_bank_name : '-',
                    'work_station' => $barcodeEntry->work_station ?? '-',
                    'ref_doc_no' => $barcodeEntry->grn_no ?? '-',
                    'details' => $groupedDetails,
                    'total_volume' => number_format($totalVolume / 1000, 2) // Convert ml to liters and format to 2 decimal places
                ]
            ];

            Log::info('Final data being sent:', ['data' => $data]);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getMiniPoolData: ' . $e->getMessage(), [
                'exception' => $e,
                'mini_pool_id' => $mini_pool_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error fetching mini pool data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tailCuttingReport(Request $request)
    {
        // If it's a GET request without ar_number, return the view
        if ($request->isMethod('get') && !$request->has('ar_number')) {
            return view('factory.generate_report.tail_cutting');
        }

        try {
            $perPage = $request->get('per_page', 80);
            $arNumber = $request->get('ar_number');

            if (!$arNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'AR Number is required'
                ]);
            }

            $bagEntries = BagEntry::with([
                'details' => function($query) {
                    $query->where('tail_cutting', 'Yes');
                },
                'miniPools',
                'createdBy',
                'bloodBank'
            ])
            ->where('ar_no', $arNumber)
            ->get();

            if ($bagEntries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected AR number'
                ]);
            }

            $allData = [];
            $rowNumber = 1;

            foreach ($bagEntries as $bagEntry) {
                // Group details by mini pool
                $miniPoolGroups = collect($bagEntry->miniPools)->groupBy('mini_pool_number');

                foreach ($miniPoolGroups as $miniPoolNumber => $miniPools) {
                    $miniPool = $miniPools->first();

                    $detailIds = is_array($miniPool->bag_entries_detail_ids)
                        ? $miniPool->bag_entries_detail_ids
                        : json_decode($miniPool->bag_entries_detail_ids, true);

                    $details = $bagEntry->details->whereIn('id', $detailIds);

                    foreach ($details as $index => $detail) {
                        $allData[] = [
                            'mega_pool_no' => $bagEntry->mega_pool_no ?? '-',
                            'mini_pool_number' => $miniPoolNumber,
                            'donor_id' => $detail->donor_id ?? '-',
                            'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                            'blood_group' => $detail->blood_group ?? '-',
                            'bag_volume_ml' => $detail->bag_volume_ml ?? '-',
                            'tail_cutting' => $detail->tail_cutting ?? '-',
                            'prepared_by' => $bagEntry->createdBy ?
                                $bagEntry->createdBy->name . ' (' . date('d/m/Y', strtotime($bagEntry->created_at)) . ')' : '-'
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $allData,
                'total' => count($allData)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in tailCuttingReport: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tailCuttingPrintTemplate(Request $request)
    {
        try {
            $arNumber = $request->get('ar_number');

            if (!$arNumber) {
                return view('factory.generate_report.tail_cutting_print', [
                    'error' => 'AR Number is required'
                ]);
            }

            $bagEntries = BagEntry::with([
                'details' => function($query) {
                    $query->where('tail_cutting', 'Yes');
                },
                'miniPools',
                'createdBy',
                'bloodBank'
            ])
            ->where('ar_no', $arNumber)
            ->get();

            if ($bagEntries->isEmpty()) {
                return view('factory.generate_report.tail_cutting_print', [
                    'error' => 'No data found for the selected AR number'
                ]);
            }

            $allData = [];
            $rowNumber = 1;

            foreach ($bagEntries as $bagEntry) {
                // Group details by mini pool
                $miniPoolGroups = collect($bagEntry->miniPools)->groupBy('mini_pool_number');

                foreach ($miniPoolGroups as $miniPoolNumber => $miniPools) {
                    $miniPool = $miniPools->first();

                    $detailIds = is_array($miniPool->bag_entries_detail_ids)
                        ? $miniPool->bag_entries_detail_ids
                        : json_decode($miniPool->bag_entries_detail_ids, true);

                    $details = $bagEntry->details->whereIn('id', $detailIds);

                    foreach ($details as $index => $detail) {
                        $allData[] = [
                            'mega_pool_no' => $bagEntry->mega_pool_no ?? '-',
                            'mini_pool_number' => $miniPoolNumber,
                            'donor_id' => $detail->donor_id ?? '-',
                            'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                            'blood_group' => $detail->blood_group ?? '-',
                            'bag_volume_ml' => $detail->bag_volume_ml ?? '-',
                            'tail_cutting' => $detail->tail_cutting ?? '-',
                            'prepared_by' => $bagEntry->createdBy ?
                                $bagEntry->createdBy->name . ' (' . date('d/m/Y', strtotime($bagEntry->created_at)) . ')' : '-'
                        ];
                    }
                }
            }

            // Get blood bank info from first bag entry
            $bloodBank = $bagEntries->first()->bloodBank->name ?? '-';
            $date = $bagEntries->first()->date ? date('d/m/Y', strtotime($bagEntries->first()->date)) : '-';

            return view('factory.generate_report.tail_cutting_print', [
                'details' => $allData,
                'bloodBank' => $bloodBank,
                'date' => $date,
                'totalRecords' => count($allData)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in tailCuttingPrintTemplate: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return view('factory.generate_report.tail_cutting_print', [
                'error' => 'Error fetching data: ' . $e->getMessage()
            ]);
        }
    }

    public function fetchARNumbers(Request $request)
    {
        try {
            $search = $request->get('q');
            $page = $request->get('page', 1);
            $perPage = 10;

            $query = BagEntry::query()
                ->whereNotNull('ar_no')
                ->where('ar_no', '!=', '');

            if ($search) {
                $query->where('ar_no', 'like', "%{$search}%");
            }

            $total = $query->count();
            $results = $query->orderBy('ar_no')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
                ->map(function($entry) {
                    return [
                        'id' => $entry->ar_no,
                        'text' => $entry->ar_no
                    ];
                });

            return response()->json([
                'items' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in fetchARNumbers: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'items' => [],
                'pagination' => [
                    'more' => false
                ]
            ]);
        }
    }

    public function printMegaPoolReport(Request $request)
    {
        try {
            $arNumber = $request->input('ar_number');

            if (!$arNumber) {
                return view('factory.generate_report.mega_pool_mini_pool_print', [
                    'error' => 'AR Number is required'
                ]);
            }

            // Get all bag entries for the AR number
            $bagEntries = BagEntry::with([
                'details' => function($query) {
                    $query->where('tail_cutting', 'Yes');
                },
                'miniPools',
                'createdBy',
                'bloodBank'
            ])
            ->where('ar_no', $arNumber)
            ->get();

            if ($bagEntries->isEmpty()) {
                return view('factory.generate_report.mega_pool_mini_pool_print', [
                    'error' => 'No data found for the selected AR number'
                ]);
            }

            // Use the first bag entry for header information
            $firstBagEntry = $bagEntries->first();
            $header = [
                'blood_centre' => $firstBagEntry->bloodBank->name ?? '-',
                'date' => $firstBagEntry->date ? date('d/m/Y', strtotime($firstBagEntry->date)) : '-',
                'pickup_date' => $firstBagEntry->pickup_date ? date('d/m/Y', strtotime($firstBagEntry->pickup_date)) : '-',
                'work_station_no' => $firstBagEntry->work_station ?? '-',
                'ar_no' => $firstBagEntry->ar_no ?? '-',
                'grn_no' => $firstBagEntry->grn_no ?? '-',
                'mega_pool' => $firstBagEntry->mega_pool_no ?? '-'
            ];

            $details = [];
            $totalVolume = 0;
            $rowNumber = 1;

            // Process all bag entries
            foreach ($bagEntries as $bagEntry) {
                foreach ($bagEntry->miniPools as $miniPool) {
                // Check if bag_entries_detail_ids is already an array
                $detailIds = is_array($miniPool->bag_entries_detail_ids)
                    ? $miniPool->bag_entries_detail_ids
                    : json_decode($miniPool->bag_entries_detail_ids, true);

                if (!is_array($detailIds)) {
                    Log::error('Invalid detail IDs format', [
                        'mini_pool_id' => $miniPool->id,
                        'bag_entries_detail_ids' => $miniPool->bag_entries_detail_ids
                    ]);
                    continue;
                }

                // Get ELISA test results
                $elisaResult = ElisaTestReport::where('mini_pool_id', $miniPool->mini_pool_number)
                    ->latest()
                    ->first();

                // Get NAT test results
                $natResult = NatTestReport::where('mini_pool_id', $miniPool->mini_pool_number)
                    ->latest()
                    ->first();

                // Determine Mini Pool Test Result
                $miniPoolTestResult = 'NON-REACTIVE';
                $megaPoolTestResult = 'NON-REACTIVE';

                if ($elisaResult) {
                    if (in_array($elisaResult->final_result, ['reactive', 'invalid', 'borderline'])) {
                        $miniPoolTestResult = 'REACTIVE';
                        $megaPoolTestResult = 'REACTIVE';
                    } elseif ($elisaResult->final_result === 'nonreactive' && $natResult) {
                        if (in_array($natResult->status, ['reactive', 'invalid', 'borderline'])) {
                            $megaPoolTestResult = 'REACTIVE';
                        }
                    }
                }

                $miniPoolDetails = BagEntryDetail::whereIn('id', $detailIds)
                    ->where('tail_cutting', 'Yes')
                    ->get();

                foreach ($miniPoolDetails as $detail) {
                    $details[] = [
                        'row_number' => $rowNumber++,
                        'bags_in_mini_pool' => $detail->bags_in_mini_pool,
                        'donor_id' => $detail->donor_id,
                        'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                        'blood_group' => $detail->blood_group,
                        'bag_volume_ml' => $detail->bag_volume_ml,
                        'mini_pool_bag_volume' => $miniPool->mini_pool_bag_volume,
                        'mini_pool_number' => $miniPool->mini_pool_number,
                        'mega_pool_no' => $bagEntry->mega_pool_no ?? '-',
                        'tail_cutting' => $detail->tail_cutting,
                        'prepared_by' => $bagEntry->createdBy ? $bagEntry->createdBy->name . ' (' . date('d/m/Y', strtotime($bagEntry->created_at)) . ')' : '-',
                        'mini_pool_test_result' => $miniPoolTestResult,
                        'mega_pool_test_result' => $megaPoolTestResult
                    ];
                }

                $totalVolume += $miniPool->mini_pool_bag_volume;
                }
            }

            return view('factory.generate_report.mega_pool_mini_pool_print', [
                'header' => $header,
                'details' => $details,
                'total_volume' => number_format($totalVolume, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in printMegaPoolReport: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return view('factory.generate_report.mega_pool_mini_pool_print', [
                'error' => 'Error fetching data: ' . $e->getMessage()
            ]);
        }
    }
}
