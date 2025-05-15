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
            $bagEntry = BagEntry::with([
                'details' => function($query) {
                    $query->where('tail_cutting', 'Yes');
                }, 
                'miniPools',
                'createdBy' // Add relation to get user who created the entry
            ])
            ->where('blood_bank_id', $request->blood_bank_id)
            ->where('pickup_date', $request->pickup_date)
            ->where('mega_pool_no', $request->mega_pool_number)
            ->first();

            if (!$bagEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected parameters'
                ]);
            }

            Log::info('fetchMegaPoolData bagEntry Response', [
                'bagEntry' => $bagEntry,
            ]);

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

            Log::info('fetchMegaPoolData bagEntryData Response', [
                'bagEntryData' => $data,
            ]);

            $totalVolume = 0;
            $rowNumber = 1;

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

                $details = BagEntryDetail::whereIn('id', $detailIds)
                    ->where('tail_cutting', 'Yes')
                    ->get();

                Log::info('fetchMegaPoolData details Response', [
                    'bagEntrydetails' => $details,
                ]);

                foreach ($details as $detail) {
                    $data['details'][] = [
                        'row_number' => $rowNumber++,
                        'bags_in_mini_pool' => $detail->bags_in_mini_pool,
                        'donor_id' => $detail->donor_id,
                        'donation_date' => $detail->donation_date ? date('d/m/Y', strtotime($detail->donation_date)) : '-',
                        'blood_group' => $detail->blood_group,
                        'bag_volume_ml' => $detail->bag_volume_ml,
                        'mini_pool_bag_volume' => $miniPool->mini_pool_bag_volume,
                        'mini_pool_number' => $miniPool->mini_pool_number,
                        'tail_cutting' => $detail->tail_cutting,
                        'prepared_by' => $bagEntry->createdBy ? $bagEntry->createdBy->name . ' (' . date('d/m/Y', strtotime($bagEntry->created_at)) . ')' : '-',
                        'mini_pool_test_result' => $miniPoolTestResult,
                        'mega_pool_test_result' => $megaPoolTestResult
                    ];
                }

                $totalVolume += $miniPool->mini_pool_bag_volume;
            }

            $data['total_volume'] = number_format($totalVolume, 2);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchMegaPoolData: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching data: ' . $e->getMessage()
            ]);
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
            // Get data from barcode_entries table
            $barcodeEntry = DB::table('barcode_entries')
                ->whereRaw("FIND_IN_SET(?, mini_pool_number)", [$mini_pool_id])
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
                    'pickup_date' => $elisaTest ? date('d/m/Y', strtotime($elisaTest->timestamp)) : '-',
                    'display_date' => $elisaTest ? date('d/m/Y', strtotime($elisaTest->timestamp)) : '-',
                    'blood_bank_name' => $plasmaEntry ? $plasmaEntry->blood_bank_name : '-',
                    'work_station' => $barcodeEntry->work_station ?? '-',
                    'ref_doc_no' => $barcodeEntry->ref_doc_no ?? '-',
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
}
