<?php

namespace App\Http\Controllers;

use App\Models\BagStatusDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BagStatusController extends Controller
{
    public function showPlasmaDespense()
    {
        try {
            $bloodCenters = BagStatusDetail::getBloodCentres();
            \Log::info('Blood Centers Data:', ['data' => $bloodCenters]);
            return view('factory.report.plasma_despense', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in showPlasmaDespense: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading blood centers data');
        }
    }

    public function getMiniPoolDetails(Request $request)
    {
        try {
            \Log::info('Request parameters for reactive:', [
                'blood_centre_name' => $request->input('blood_centre_name'),
                'pickup_date' => $request->input('pickup_date')
            ]);

            $bloodCentreName = $request->input('blood_centre_name');
            $city = $request->input('city');
            $pickupDate = $request->input('pickup_date');

            // Extract blood bank name without city
            if ($bloodCentreName) {
                $parts = explode('(', $bloodCentreName);
                $bloodCentreName = trim($parts[0]);
            }

            $miniPoolDetails = BagStatusDetail::getMiniPoolDetails(
                $bloodCentreName,
                $pickupDate
            );

            return response()->json([
                'status' => 'success',
                'data' => $miniPoolDetails
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getMiniPoolDetails: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching mini pool details'
            ], 500);
        }
    }

        public function getNonReactiveMiniPoolDetails(Request $request)
    {
        try {
            \Log::info('Request parameters for non-reactive:', [
                'ar_no' => $request->input('ar_no')
            ]);

            $ar_no = $request->input('ar_no');

            if ($ar_no) {
                // First check if we have any draft data for this AR number
                $draftExists = BagStatusDetail::where('ar_no', $ar_no)
                    ->where('status_type', 'draft')
                    ->exists();

                // Then check if we have any release data
                $releaseExists = BagStatusDetail::where('ar_no', $ar_no)
                    ->where('status_type', 'release')
                    ->where('release_status', 'approved')
                    ->exists();

                // Get both draft and approved release records
                $draftData = collect([]);
                $releaseData = collect([]);

                // Get draft data if it exists
                if ($draftExists) {
                    $draftData = BagStatusDetail::where('ar_no', $ar_no)
                        ->where('status_type', 'draft')
                        ->get();
                }

                // Get approved release data if it exists
                if ($releaseExists) {
                    $releaseData = BagStatusDetail::where('ar_no', $ar_no)
                        ->where('status_type', 'release')
                        ->where('release_status', 'approved')
                        ->get();
                }

                // If we have either draft or release data
                if ($draftExists || $releaseExists) {
                    // Combine both collections, with draft data first
                    $data = $draftData->concat($releaseData);
                    $is_draft = $draftExists; // Set is_draft based on if we have draft data
                } else {
                    // Get blood bank info
                    $bloodBankInfo = \DB::table('plasma_entries')
                        ->select('entities.name as blood_bank_name')
                        ->leftJoin('entities', 'entities.id', '=', 'plasma_entries.blood_bank_id')
                        ->where('plasma_entries.alloted_ar_no', $ar_no)
                        ->first();

                    $bloodBankName = $bloodBankInfo ? $bloodBankInfo->blood_bank_name : '';

                    $miniPoolDetails = BagStatusDetail::getNonReactiveMiniPoolDetails(
                        $ar_no
                    );

                    return response()->json([
                        'status' => 'success',
                        'data' => $miniPoolDetails,
                        'blood_bank_name' => $bloodBankName,
                        'is_draft' => false
                    ]);
                }

                // Process draft or release data
                // Get the blood bank name
                $bloodBankName = '';
                if (count($data) > 0 && $data[0]->blood_bank_id) {
                    $bloodBankInfo = \DB::table('entities')
                        ->where('id', $data[0]->blood_bank_id)
                        ->first();
                    $bloodBankName = $bloodBankInfo ? $bloodBankInfo->name : '';
                }

                // Transform data to match expected format
                $transformedResults = $data->map(function($item) {
                    // Set is_draft flag based on the item's status_type
                    $itemIsDraft = $item->status_type === 'draft';

                    return [
                        'ar_no' => $item->ar_no,
                        'mega_pool_no' => $item->mini_pool_id, // Using mini_pool_id as mega_pool_no
                        'volume_in_ltrs' => $item->total_volume ? number_format($item->total_volume, 2) : '0.00',
                        'issued_volume' => $item->issued_volume ? number_format($item->issued_volume, 2) : '',
                        'is_draft' => $itemIsDraft,
                        'status_type' => $item->status_type,
                        'batch_no' => $item->batch_no,
                        'date' => $item->date ? $item->date->format('Y-m-d') : null
                    ];
                });

                return response()->json([
                    'status' => 'success',
                    'data' => $transformedResults,
                    'blood_bank_name' => $bloodBankName,
                    'is_draft' => $is_draft,
                    'batch_no' => count($transformedResults) > 0 ? $transformedResults[0]['batch_no'] : null,
                    'date' => count($transformedResults) > 0 ? $transformedResults[0]['date'] : null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AR number is required'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error in getNonReactiveMiniPoolDetails controller: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching non-reactive mini pool details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBloodCentres()
    {
        $bloodCentres = BagStatusDetail::getBloodCentres();
        return response()->json([
            'status' => 'success',
            'data' => $bloodCentres
        ]);
    }

    public function storePlasmaRejection(Request $request)
    {
        try {
            \Log::info('Plasma rejection request data:', $request->all());

            // Log the route that was accessed
            \Log::info('Route accessed:', [
                'route' => $request->route() ? $request->route()->getName() : 'No route name',
                'method' => $request->method(),
                'uri' => $request->getRequestUri()
            ]);

            // Special debugging - write to a file that we definitely can check
            file_put_contents(
                storage_path('debug_request.txt'),
                date('Y-m-d H:i:s') . "\n" .
                "URI: " . $request->getRequestUri() . "\n" .
                "Method: " . $request->method() . "\n" .
                "Data: " . json_encode($request->all(), JSON_PRETTY_PRINT) . "\n\n",
                FILE_APPEND
            );

            // Simplify validation for troubleshooting
            $request->validate([
                'ar_number' => 'required',
            ]);

            DB::beginTransaction();

            // Get the plasma entry to get blood bank and other details
            $plasmaEntry = DB::table('plasma_entries')
                ->where('alloted_ar_no', $request->ar_number)
                ->first();

            \Log::info('Plasma entry found:', ['entry' => $plasmaEntry]);

            if (!$plasmaEntry) {
                throw new \Exception("No plasma entry found for AR number: {$request->ar_number}");
            }

            // Generate destruction number
            $year = date('Y');
            $lastDestructionNo = DB::table('plasma_entries_destruction')
                ->where('destruction_no', 'LIKE', "DES/{$year}/%")
                ->whereNotNull('destruction_no')
                ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(destruction_no, "/", -1) AS UNSIGNED)'), 'DESC')
                ->first();

            $lastSequence = 0;
            if ($lastDestructionNo && $lastDestructionNo->destruction_no) {
                $parts = explode('/', $lastDestructionNo->destruction_no);
                $lastSequence = intval(end($parts));
            }

            $sequence = $lastSequence + 1;
            $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $destructionNo = "DES/{$year}/{$formattedSequence}";

            // Calculate total bag volume
            $totalBagVolume = 0;
            if ($request->has('bag_volume')) {
                foreach ($request->bag_volume as $volume) {
                    $totalBagVolume += floatval($volume);
                }
            }

            \Log::info('Ready to process rows', [
                'donor_ids' => $request->has('donor_id') ? count($request->donor_id) : 0,
                'destruction_no' => $destructionNo,
                'total_bag_volume' => $totalBagVolume
            ]);

            // Process each row of data
            if ($request->has('donor_id')) {
                for ($i = 0; $i < count($request->donor_id); $i++) {
                    // Skip empty rows
                    if (empty($request->donor_id[$i]) && empty($request->bag_volume[$i])) {
                        continue;
                    }

                    $rowData = [
                        'pickup_date' => $plasmaEntry->pickup_date,
                        'reciept_date' => $plasmaEntry->reciept_date,
                        'grn_no' => $plasmaEntry->grn_no,
                        'blood_bank_id' => $plasmaEntry->blood_bank_id,
                        'plasma_qty' => $plasmaEntry->plasma_qty,
                        'ar_no' => $request->ar_number,
                        'total_bag_val' => $totalBagVolume,
                        'destruction_no' => $destructionNo,
                        'donor_id' => $request->donor_id[$i],
                        'donation_date' => !empty($request->donation_date[$i]) ? $request->donation_date[$i] : null,
                        'blood_group' => isset($request->blood_group[$i]) ? $request->blood_group[$i] : null,
                        'bag_volume_ml' => isset($request->bag_volume[$i]) ? $request->bag_volume[$i] : null,
                        'reject_reason' => isset($request->remarks[$i]) ? $request->remarks[$i] : null,
                        'created_by' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    \Log::info('Inserting row data:', ['row' => $i, 'data' => $rowData]);

                    // Save to plasma_entries_destruction table
                    DB::table('plasma_entries_destruction')->insert($rowData);
                }
            }

            // Update the original plasma entry with destruction number
            DB::table('plasma_entries')
                ->where('alloted_ar_no', $request->ar_number)
                ->update([
                    'destruction_no' => $destructionNo,
                    'updated_by' => auth()->id(),
                    'updated_at' => now()
                ]);

            DB::commit();
            \Log::info('Transaction committed successfully');

            // Check if request wants JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Plasma rejection records saved successfully',
                    'destruction_no' => $destructionNo
                ]);
            }

            return redirect()->back()->with('success', 'Plasma rejection records saved successfully with Destruction No: ' . $destructionNo);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in storePlasmaRejection: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Check if request wants JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving plasma rejection records: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'Error saving plasma rejection records: ' . $e->getMessage());
        }
    }

    public function storePlasmaDespense(Request $request)
    {
        try {
            \Log::info('Plasma Despense Request Data:', [
                'all' => $request->all(),
                'mini_pools' => $request->input('mini_pools'),
                'ar_no' => $request->input('ar_no'),
                'batch_no' => $request->input('batch_no'),
                'date' => $request->input('date'),
            ]);

            $request->validate([
                'ar_no' => 'required|string',
                'batch_no' => 'required|string',
                'date' => 'required|date',
                'mini_pools' => 'required|array',
                'mini_pools.*.mini_pool_id' => 'required',
                'mini_pools.*.issued_volume' => 'nullable|numeric',
                'mini_pools.*.remarks' => 'nullable|string',
                'total_requested' => 'nullable|numeric',
                'total_issued' => 'nullable|numeric',
                'is_draft' => 'nullable|in:0,1,true,false'
            ]);

            DB::beginTransaction();

            // Check if this is a draft submission
            $isDraft = $request->input('is_draft') == '1' || $request->input('is_draft') === true || $request->input('is_draft') === 1;

            if ($isDraft) {
                // For draft mode, we'll check if there are existing records in bag_status_details
                $existingRecord = DB::table('bag_status_details')
                    ->where('ar_no', $request->ar_no)
                    ->first();

                if ($existingRecord && $existingRecord->blood_bank_id) {
                    // Use the blood_bank_id from existing record
                    $bloodBankId = $existingRecord->blood_bank_id;
                    \Log::info("Using blood_bank_id from existing bag_status_details record: {$bloodBankId}");
                } else {
                    // Try to get from plasma_entries as fallback
                    $plasmaEntry = DB::table('plasma_entries')
                        ->where('alloted_ar_no', $request->ar_no)
                        ->first();

                    if (!$plasmaEntry) {
                        throw new \Exception("No plasma entry found for AR number: {$request->ar_no}");
                    }

                    $bloodBankId = $plasmaEntry->blood_bank_id;
                }
            } else {
                // For final submission, first check if there are existing records in bag_status_details
                $existingRecord = DB::table('bag_status_details')
                    ->where('ar_no', $request->ar_no)
                    ->first();

                if ($existingRecord && $existingRecord->blood_bank_id) {
                    // Use the blood_bank_id from existing record
                    $bloodBankId = $existingRecord->blood_bank_id;
                    \Log::info("Using blood_bank_id from existing bag_status_details record for final submission: {$bloodBankId}");
                } else {
                    // Try to get from plasma_entries
                    $plasmaEntry = DB::table('plasma_entries')
                        ->where('alloted_ar_no', $request->ar_no)
                        ->first();

                    if (!$plasmaEntry) {
                        throw new \Exception("No plasma entry found for AR number: {$request->ar_no}");
                    }

                    // Try to get from bag_entries as well
                    $bagEntry = DB::table('bag_entries')
                        ->where('ar_no', $request->ar_no)
                        ->first();

                    // Use bag_entry blood_bank_id if available, otherwise use plasma_entry
                    $bloodBankId = $bagEntry ? $bagEntry->blood_bank_id : $plasmaEntry->blood_bank_id;

                    \Log::info("Using blood_bank_id for final submission:", [
                        'source' => $bagEntry ? 'bag_entries' : 'plasma_entries',
                        'blood_bank_id' => $bloodBankId
                    ]);
                }
            }

            // Check if this is a draft or final submission
            $statusType = $request->input('is_draft') == '1' || $request->input('is_draft') === true || $request->input('is_draft') === 1 ? 'draft' : 'final';

            \Log::info('Draft Status Check:', [
                'is_draft_raw' => $request->input('is_draft'),
                'is_draft_type' => gettype($request->input('is_draft')),
                'status_type_result' => $statusType
            ]);

            // Calculate total issued volume
            $totalIssuedVolume = 0;
            foreach ($request->mini_pools as $miniPool) {
                if (isset($miniPool['issued_volume']) && is_numeric($miniPool['issued_volume'])) {
                    $totalIssuedVolume += floatval($miniPool['issued_volume']);
                }
            }

            // Filter out any invalid mini_pool_id values
            $validMiniPools = [];
            foreach ($request->mini_pools as $key => $miniPool) {
                // Skip any entries where mini_pool_id is missing, numeric, or the string 'undefined'
                if (!isset($miniPool['mini_pool_id']) ||
                    $miniPool['mini_pool_id'] === 'undefined' ||
                    $miniPool['mini_pool_id'] === '-' ||
                    $miniPool['mini_pool_id'] === '') {
                    \Log::warning('Skipping invalid mini_pool_id:', ['miniPool' => $miniPool]);
                    continue;
                }
                $validMiniPools[] = $miniPool;
            }

            \Log::info('Filtered mini pools:', [
                'original_count' => count($request->mini_pools),
                'valid_count' => count($validMiniPools),
                'valid_mini_pools' => $validMiniPools
            ]);

            // Check if we have existing draft records for this AR number
            $existingDrafts = BagStatusDetail::where('ar_no', $request->ar_no)
                ->where('status_type', 'draft')
                ->get();

            \Log::info('Existing draft records:', [
                'count' => $existingDrafts->count(),
                'records' => $existingDrafts->toArray()
            ]);

            // For final submissions, we'll handle draft records individually when processing each mini pool
            // so we don't need to delete them all here. We'll keep track of which ones we've processed.
            $processedDraftIds = [];

            // Create an index of existing drafts by mini_pool_id for easier lookup
            $existingDraftsIndex = [];
            foreach ($existingDrafts as $draft) {
                $existingDraftsIndex[$draft->mini_pool_id] = $draft;
            }

            // Process each mini pool - update existing drafts or create new records
            foreach ($validMiniPools as $miniPool) {
                $miniPoolId = $miniPool['mini_pool_id'];

                // For draft mode, check if record exists by mini_pool_id and ar_no
                if ($statusType === 'draft') {
                    // Try to find existing record by mini_pool_id and ar_no
                    $existingRecord = BagStatusDetail::where('mini_pool_id', $miniPoolId)
                        ->where('ar_no', $request->ar_no)
                        ->first();

                    if ($existingRecord) {
                        // Update the existing record
                        $existingRecord->update([
                            'batch_no' => $request->batch_no,
                            'date' => $request->date,
                            'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null,
                            'total_volume' => $request->total_requested,
                            'total_issued_volume' => $totalIssuedVolume,
                            'status_type' => 'draft',
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);

                        \Log::info('Updated existing record by mini_pool_id and ar_no:', [
                            'id' => $existingRecord->id,
                            'mini_pool_id' => $miniPoolId,
                            'ar_no' => $request->ar_no,
                            'updates' => [
                                'batch_no' => $request->batch_no,
                                'date' => $request->date,
                                'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null
                            ]
                        ]);
                    } else {
                        // Create a new record
                        $newRecord = BagStatusDetail::create([
                            'mini_pool_id' => $miniPoolId,
                            'blood_bank_id' => $bloodBankId,
                            'ar_no' => $request->ar_no,
                            'batch_no' => $request->batch_no,
                            'date' => $request->date,
                            'status' => 'despense',
                            'status_type' => 'draft',
                            'timestamp' => now(),
                            'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null,
                            'total_volume' => $request->total_requested,
                            'total_issued_volume' => $totalIssuedVolume,
                            'created_by' => auth()->id()
                        ]);

                        \Log::info('Created new draft record:', [
                            'id' => $newRecord->id,
                            'mini_pool_id' => $miniPoolId,
                            'ar_no' => $request->ar_no
                        ]);
                    }
                } else {
                    // For final submission, first check if a draft record exists for this mini_pool_id and ar_no
                    $existingDraft = BagStatusDetail::where('mini_pool_id', $miniPoolId)
                        ->where('ar_no', $request->ar_no)
                        ->where('status_type', 'draft')
                        ->first();

                    // If no draft exists, check for any record with this mini_pool_id and ar_no
                    $existingRecord = $existingDraft ?: BagStatusDetail::where('mini_pool_id', $miniPoolId)
                        ->where('ar_no', $request->ar_no)
                        ->first();

                    if ($existingRecord) {
                        // If this was a draft record, add its ID to processed list
                        if ($existingDraft) {
                            $processedDraftIds[] = $existingDraft->id;
                        }

                        // Update the existing record
                        $existingRecord->update([
                            'batch_no' => $request->batch_no,
                            'date' => $request->date,
                            'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null,
                            'total_volume' => $request->total_requested,
                            'total_issued_volume' => $totalIssuedVolume,
                            'status_type' => 'final',
                            'updated_by' => auth()->id(),
                            'updated_at' => now()
                        ]);

                        \Log::info('Updated existing record for final submission:', [
                            'id' => $existingRecord->id,
                            'was_draft' => $existingDraft ? true : false,
                            'mini_pool_id' => $miniPoolId,
                            'ar_no' => $request->ar_no,
                            'updates' => [
                                'batch_no' => $request->batch_no,
                                'date' => $request->date,
                                'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null
                            ]
                        ]);
                    } else {
                        // Create a new record
                        $newRecord = BagStatusDetail::create([
                            'mini_pool_id' => $miniPoolId,
                            'blood_bank_id' => $bloodBankId,
                            'ar_no' => $request->ar_no,
                            'batch_no' => $request->batch_no,
                            'date' => $request->date,
                            'status' => 'despense',
                            'status_type' => 'final',
                            'timestamp' => now(),
                            'issued_volume' => isset($miniPool['issued_volume']) ? $miniPool['issued_volume'] : null,
                            'total_volume' => $request->total_requested,
                            'total_issued_volume' => $totalIssuedVolume,
                            'created_by' => auth()->id()
                        ]);

                        \Log::info('Created new final record:', [
                            'id' => $newRecord->id,
                            'mini_pool_id' => $miniPoolId,
                            'ar_no' => $request->ar_no
                        ]);
                    }
                }
            }

            // Handle cleanup of draft records
            if ($statusType === 'draft') {
                // For draft mode - if there are any old drafts that weren't updated, delete them
                $currentMiniPoolIds = collect($validMiniPools)->pluck('mini_pool_id')->toArray();
                $toDelete = $existingDrafts->filter(function($draft) use ($currentMiniPoolIds) {
                    return !in_array($draft->mini_pool_id, $currentMiniPoolIds);
                });

                if ($toDelete->isNotEmpty()) {
                    \Log::info('Deleting outdated draft records:', [
                        'count' => $toDelete->count(),
                        'ids' => $toDelete->pluck('id')->toArray(),
                        'mini_pool_ids' => $toDelete->pluck('mini_pool_id')->toArray()
                    ]);

                    foreach ($toDelete as $draft) {
                        $draft->delete();
                    }
                }
            } else {
                // For final submission - delete any draft records that weren't processed
                // (meaning they weren't updated to final status)
                if ($existingDrafts->isNotEmpty()) {
                    $unprocessedDrafts = $existingDrafts->filter(function($draft) use ($processedDraftIds) {
                        return !in_array($draft->id, $processedDraftIds);
                    });

                    if ($unprocessedDrafts->isNotEmpty()) {
                        \Log::info('Deleting unprocessed draft records after final submission:', [
                            'count' => $unprocessedDrafts->count(),
                            'ids' => $unprocessedDrafts->pluck('id')->toArray(),
                            'mini_pool_ids' => $unprocessedDrafts->pluck('mini_pool_id')->toArray()
                        ]);

                        foreach ($unprocessedDrafts as $draft) {
                            $draft->delete();
                        }
                    }
                }
            }

            DB::commit();

            $message = $statusType === 'draft' ?
                'Plasma despense records saved as draft successfully' :
                'Plasma despense records saved successfully';

            return response()->json([
                'status' => 'success',
                'message' => $message,
                'status_type' => $statusType
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in storePlasmaDespense: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving plasma despense records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store plasma rejection records (from bag_status_details and elisa_test_report)
     */
    public function storeNatRejection(Request $request)
    {
        try {
            \Log::info('Plasma rejection request data:', $request->all());

            // Validate request
            $request->validate([
                'ar_number' => 'required',
                'items' => 'required|array',
                'items.*.id' => 'required',
                'items.*.remarks' => 'nullable',
                'items.*.source_type' => 'required|in:bag_status,elisa',
                'items.*.mini_pool_id' => 'required',
            ]);

            DB::beginTransaction();

            // Get the plasma entry to get blood bank and other details
            $plasmaEntry = DB::table('plasma_entries')
                ->where('alloted_ar_no', $request->ar_number)
                ->first();

            \Log::info('Plasma entry found:', ['entry' => $plasmaEntry]);

            if (!$plasmaEntry) {
                throw new \Exception("No plasma entry found for AR number: {$request->ar_number}");
            }

            $savedCount = 0;

            // Process each item
            foreach ($request->items as $item) {
                // Skip if no remark is provided for this item
                if (empty($item['remarks'])) {
                    continue;
                }

                // Check if already exists in plasma_entries_destruction
                $existingRecord = DB::table('plasma_entries_destruction')
                    ->where('ar_no', $request->ar_number)
                    ->where('mega_pool_id', $item['mini_pool_id'])
                    ->where('reject_reason', $item['remarks'])
                    ->first();

                if ($existingRecord) {
                    \Log::info('Record already exists, skipping:', [
                        'mini_pool_id' => $item['mini_pool_id'],
                        'reject_reason' => $item['remarks']
                    ]);
                    continue;
                }

                // Insert record into plasma_entries_destruction
                DB::table('plasma_entries_destruction')->insert([
                    'blood_bank_id' => $plasmaEntry->blood_bank_id,
                    'plasma_qty' => $plasmaEntry->plasma_qty,
                    'ar_no' => $request->ar_number,
                    'mega_pool_id' => $item['mini_pool_id'],
                    'reject_reason' => $item['remarks'],
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $savedCount++;

                \Log::info('Saved rejection record:', [
                    'source_type' => $item['source_type'],
                    'mini_pool_id' => $item['mini_pool_id'],
                    'reject_reason' => $item['remarks']
                ]);
            }

            DB::commit();
            \Log::info('Plasma rejection transaction committed successfully', ['saved_count' => $savedCount]);

            // Check if request wants JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully saved {$savedCount} plasma rejection record(s)"
                ]);
            }

            return redirect()->back()->with('success', "Successfully saved {$savedCount} plasma rejection record(s)");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in storeNatRejection: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Check if request wants JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving plasma rejection records: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()->with('error', 'Error saving plasma rejection records: ' . $e->getMessage());
        }
    }

    public function getArNumbers()
    {
        try {
            $arNumbers = DB::table('bag_status_details')
                ->select('ar_no')
                ->distinct()
                ->whereNotNull('ar_no')
                ->where('ar_no', '!=', '')
                ->orderBy('ar_no', 'desc')
                ->get();

            return response()->json($arNumbers, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching AR numbers: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([], 500);
        }
    }

    public function getPlasmaRejectionDetails(Request $request)
    {
        try {
            $arNumber = $request->input('ar_number');

            if (!$arNumber) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'AR number is required'
                ], 400);
            }

            // Get blood bank info and pickup date from plasma entries
            $plasmaEntry = DB::table('plasma_entries')
                ->select('entities.name as blood_centre', 'plasma_entries.pickup_date')
                ->leftJoin('entities', 'entities.id', '=', 'plasma_entries.blood_bank_id')
                ->where('plasma_entries.alloted_ar_no', $arNumber)
                ->first();

            if (!$plasmaEntry) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No plasma entry found for the provided AR number'
                ], 404);
            }

            // Get details from bag_status_details where reject_status is approved
            // Exclude records that are already in plasma_entries_destruction
            $bagStatusDetails = DB::table('bag_status_details')
                ->select(
                    'id',
                    'mini_pool_id',
                    'ar_no',
                    'date as donation_date',
                    DB::raw("'rejected' as status"),
                    'reject_status',
                    'status_type',
                    DB::raw("'bag_status' as source_type")
                )
                ->where('ar_no', $arNumber)
                ->where('status_type', 'release')
                ->where('reject_status', 'approved')
                ->whereNotExists(function($query) use ($arNumber) {
                    $query->select(DB::raw(1))
                        ->from('plasma_entries_destruction')
                        ->whereRaw('plasma_entries_destruction.mega_pool_id = bag_status_details.mini_pool_id')
                        ->where('plasma_entries_destruction.ar_no', $arNumber);
                })
                ->get();

            // Get details from elisa_test_report where BOTH HIV AND HCV are reactive
            // Join with barcode_entries using FIND_IN_SET because mini_pool_number is a comma-separated string
            // Exclude records that are already in plasma_entries_destruction
            $elisaDetails = DB::table('elisa_test_report')
                ->select(
                    'elisa_test_report.id',
                    'elisa_test_report.mini_pool_id',
                    'barcode_entries.ar_no',
                    'elisa_test_report.result_time as donation_date',
                    DB::raw("'HIV & HCV Reactive' as status"),
                    DB::raw("NULL as reject_status"),
                    DB::raw("'elisa' as status_type"),
                    DB::raw("'elisa' as source_type")
                )
                ->join('barcode_entries', function($join) {
                    $join->whereRaw('FIND_IN_SET(elisa_test_report.mini_pool_id, barcode_entries.mini_pool_number) > 0');
                })
                ->where('barcode_entries.ar_no', $arNumber)
                ->where('elisa_test_report.hiv', 'reactive')
                ->where('elisa_test_report.hcv', 'reactive')
                ->whereNull('elisa_test_report.deleted_at')
                ->whereNotExists(function($query) use ($arNumber) {
                    $query->select(DB::raw(1))
                        ->from('plasma_entries_destruction')
                        ->whereRaw('plasma_entries_destruction.mega_pool_id = elisa_test_report.mini_pool_id')
                        ->where('plasma_entries_destruction.ar_no', $arNumber);
                })
                ->get();

            // Combine both results
            $plasmaDetails = $bagStatusDetails->concat($elisaDetails);

            // Format the pickup date if it exists
            $pickupDate = null;
            if ($plasmaEntry && $plasmaEntry->pickup_date) {
                $pickupDate = date('d-m-Y', strtotime($plasmaEntry->pickup_date));
            }

            return response()->json([
                'status' => 'success',
                'data' => $plasmaDetails,
                'blood_centre' => $plasmaEntry ? $plasmaEntry->blood_centre : null,
                'pickup_date' => $pickupDate
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getPlasmaRejectionDetails: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching plasma rejection details: ' . $e->getMessage()
            ], 500);
        }
    }
}
