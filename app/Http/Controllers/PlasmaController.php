<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\PlasmaEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BagStatusDetail;

class PlasmaController extends Controller
{
    /**
     * Display the plasma dispensing form.
     *
     * @return \Illuminate\View\View
     */
    public function dispensing()
    {
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

        return view('factory.plasma_management.plasma_dispensing', compact('bloodCenters'));
    }

    /**
     * Display the plasma entry form.
     *
     * @return \Illuminate\View\View
     */
    public function plasmaEntry()
    {
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
            
        $user = Auth::user();
        $userName = $user ? $user->name : '';
            
        return view('factory.plasma_management.plasma_entry', compact('bloodCenters', 'userName'));
    }

    /**
     * Store plasma entries.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'pickup_date.*' => 'nullable|date',
                'receipt_date.*' => 'nullable|date',
                'grn_no.*' => 'nullable|string',
                'blood_bank.*' => 'nullable|exists:entities,id',
                'plasma_qty.*' => 'nullable|numeric|min:0',
                'remarks.*' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $userId = Auth::id();
            $entries = [];

            foreach ($request->receipt_date as $index => $date) {
                // Skip empty rows
                if (empty($date) && empty($request->pickup_date[$index]) && empty($request->grn_no[$index]) && 
                    empty($request->blood_bank[$index]) && empty($request->plasma_qty[$index]) && 
                    empty($request->remarks[$index])) {
                    continue;
                }

                $entry = PlasmaEntry::create([
                    'pickup_date' => $request->pickup_date[$index],
                    'reciept_date' => $date,
                    'grn_no' => $request->grn_no[$index],
                    'blood_bank_id' => $request->blood_bank[$index],
                    'plasma_qty' => $request->plasma_qty[$index],
                    'remarks' => $request->remarks[$index],
                    'created_by' => $userId,
                ]);

                $entries[] = $entry;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Plasma entries saved successfully',
                'data' => $entries
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving plasma entries: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save plasma entries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the AR number generation form.
     *
     * @return \Illuminate\View\View
     */
    public function generateArNo()
    {
        $plasmaEntries = PlasmaEntry::with(['bloodBank', 'creator'])
            ->whereNull('alloted_ar_no')
            ->whereNull('destruction_no')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($entry) {
                return [
                    'id' => $entry->id,
                    'pickup_date' => $entry->pickup_date ? $entry->pickup_date->format('Y-m-d') : null,
                    'receipt_date' => $entry->reciept_date ? $entry->reciept_date->format('Y-m-d') : null,
                    'grn_no' => $entry->grn_no,
                    'blood_bank' => $entry->bloodBank ? $entry->bloodBank->name : null,
                    'blood_bank_id' => $entry->blood_bank_id,
                    'plasma_qty' => $entry->plasma_qty,
                    'ar_no' => $entry->alloted_ar_no,
                    'destruction_no' => $entry->destruction_no,
                    'entered_by' => $entry->creator ? $entry->creator->name : null,
                    'remarks' => $entry->remarks,
                    'status' => $entry->status ?? 'pending'
                ];
            });

        Log::info('Fetched plasma entries:', [
            'count' => $plasmaEntries->count(),
            'entries' => $plasmaEntries->toArray()
        ]);

        return view('factory.plasma_management.generate_ar_no', compact('plasmaEntries'));
    }

    /**
     * Get blood banks for Select2 AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBloodBanks(Request $request)
    {
        try {
            $search = $request->get('search');
            $query = Entity::with(['city', 'state'])
                ->where('entity_type_id', 2)
                ->where('account_status', 'active');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('city', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('state', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $bloodBanks = $query->select('id', 'name', 'city_id', 'state_id')
                ->orderBy('name')
                ->get()
                ->map(function($bank) {
                    return [
                        'id' => $bank->id,
                        'text' => $bank->name . ' (' . optional($bank->city)->name . ', ' . optional($bank->state)->name . ')'
                    ];
                });

            Log::info('Blood banks query result:', [
                'search' => $search,
                'count' => $bloodBanks->count(),
                'results' => $bloodBanks
            ]);

            return response()->json([
                'results' => $bloodBanks->values()->all()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching blood banks: ' . $e->getMessage(), [
                'exception' => $e,
                'search' => $request->get('search')
            ]);

            return response()->json([
                'error' => 'Failed to fetch blood banks',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate AR Number based on SOP
     * Format: AR/RM10001/XXXX/YY/NNNN
     * where:
     * AR – Analytical Report
     * RM10001- Code for Human Plasma
     * XXXX- last four digits of the plasma blood bank code
     * YY - indicates the last two digits of the calendar year
     * NNNN - sequential number starting from 0001 (resets each year)
     */
    private function generateArNumber($bloodBankId)
    {
        try {
            // Format blood bank ID to 4 digits
            $bloodBankCode = str_pad($bloodBankId, 4, '0', STR_PAD_LEFT);
            
            // Get current year's last 2 digits
            $year = date('y');
            
            // Get the last AR number for this blood bank and year
            $lastArNo = DB::table('plasma_entries')
                ->where('alloted_ar_no', 'LIKE', "AR/RM10001/{$bloodBankCode}/{$year}/%")
                ->whereNotNull('alloted_ar_no')
                ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(alloted_ar_no, "/", -1) AS UNSIGNED)'), 'DESC')
                ->lockForUpdate()
                ->first();

            // Generate sequential number
            if ($lastArNo && $lastArNo->alloted_ar_no) {
                // Extract the last sequence number from the AR number
                $parts = explode('/', $lastArNo->alloted_ar_no);
                $lastSequence = intval(end($parts));
                $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
            } else {
                // If no previous AR number exists for this blood bank and year, start with 0001
                $sequence = '0001';
            }
            
            // Generate the AR number
            $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";
            
            // Verify this AR number doesn't exist (double-check)
            $exists = DB::table('plasma_entries')
                ->where('alloted_ar_no', $arNo)
                ->exists();
                
            if ($exists) {
                throw new \Exception("AR Number {$arNo} already exists. Please try again.");
            }
            
            return $arNo;
            
        } catch (\Exception $e) {
            Log::error('Error generating AR number: ' . $e->getMessage(), [
                'blood_bank_id' => $bloodBankId,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Update AR number for a plasma entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateArNo(Request $request)
    {
        try {
            Log::info('Received request for updateArNo:', [
                'request_data' => $request->all()
            ]);

            // Handle bulk save
            if ($request->has('bulk_save')) {
                $request->validate([
                    'entries' => 'required|array',
                    'entries.*.entry_id' => 'required|exists:plasma_entries,id',
                    'entries.*.remarks' => 'nullable|string',
                    'entries.*.status' => 'required|in:accepted'
                ]);

                DB::beginTransaction();

                try {
                    // Group entries by blood bank ID
                    $entriesByBloodBank = collect($request->entries)->groupBy(function($entry) {
                        return PlasmaEntry::find($entry['entry_id'])->blood_bank_id;
                    });

                    // Process each blood bank's entries
                    foreach ($entriesByBloodBank as $bloodBankId => $entries) {
                        // Get the last AR number for this blood bank and year
                        $bloodBankCode = str_pad($bloodBankId, 4, '0', STR_PAD_LEFT);
                        $year = date('y');
                        
                        $lastArNo = DB::table('plasma_entries')
                            ->where('alloted_ar_no', 'LIKE', "AR/RM10001/{$bloodBankCode}/{$year}/%")
                            ->whereNotNull('alloted_ar_no')
                            ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(alloted_ar_no, "/", -1) AS UNSIGNED)'), 'DESC')
                            ->lockForUpdate()
                            ->first();

                        $lastSequence = 0;
                        if ($lastArNo && $lastArNo->alloted_ar_no) {
                            $parts = explode('/', $lastArNo->alloted_ar_no);
                            $lastSequence = intval(end($parts));
                        }

                        // Generate AR numbers for each entry in this blood bank
                        foreach ($entries as $entry) {
                            $plasmaEntry = PlasmaEntry::findOrFail($entry['entry_id']);
                            
                            // Increment sequence number
                            $lastSequence++;
                            $sequence = str_pad($lastSequence, 4, '0', STR_PAD_LEFT);
                            
                            // Generate AR number
                            $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";
                            
                            // Verify AR number doesn't exist
                            $exists = PlasmaEntry::where('alloted_ar_no', $arNo)->exists();
                            if ($exists) {
                                throw new \Exception("AR Number {$arNo} already exists. Please try again.");
                            }
                            
                            // Update the entry
                            $plasmaEntry->update([
                                'alloted_ar_no' => $arNo,
                                'remarks' => $entry['remarks'],
                                'status' => $entry['status'],
                                'updated_by' => Auth::id()
                            ]);
                        }
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'All AR numbers have been saved successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            // Handle single entry AR number generation
            $request->validate([
                'entry_id' => 'required|exists:plasma_entries,id',
                'remarks' => 'nullable|string',
                'status' => 'required|in:accepted',
                'generate_only' => 'nullable|boolean',
                'preview_ar_no' => 'nullable|string',
                'blood_bank_id' => 'required|exists:entities,id',
                'current_sequence' => 'nullable|integer'
            ]);

            DB::beginTransaction();

            try {
                $entry = PlasmaEntry::findOrFail($request->entry_id);
                $bloodBankId = $request->blood_bank_id;
                $bloodBankCode = str_pad($bloodBankId, 4, '0', STR_PAD_LEFT);
                $year = date('y');
                
                // Get the last AR number for this blood bank and year
                $lastArNo = DB::table('plasma_entries')
                    ->where('alloted_ar_no', 'LIKE', "AR/RM10001/{$bloodBankCode}/{$year}/%")
                    ->whereNotNull('alloted_ar_no')
                    ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(alloted_ar_no, "/", -1) AS UNSIGNED)'), 'DESC')
                    ->lockForUpdate()
                    ->first();

                $lastSequence = 0;
                if ($lastArNo && $lastArNo->alloted_ar_no) {
                    $parts = explode('/', $lastArNo->alloted_ar_no);
                    $lastSequence = intval(end($parts));
                }

                // Use the higher sequence number between client and server
                $sequence = max($lastSequence + 1, $request->current_sequence ?? 0);
                $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                
                // Generate AR number
                $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";
                
                // Verify AR number doesn't exist
                $exists = PlasmaEntry::where('alloted_ar_no', $arNo)->exists();
                if ($exists) {
                    throw new \Exception("AR Number {$arNo} already exists. Please try again.");
                }

                // If generate_only is true, just return the AR number without saving
                if ($request->boolean('generate_only')) {
                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'AR number generated successfully',
                        'ar_no' => $arNo,
                        'sequence' => intval($sequence)
                    ]);
                }

                // Update the entry
                $entry->update([
                    'alloted_ar_no' => $arNo,
                    'remarks' => $request->remarks,
                    'status' => $request->status,
                    'updated_by' => Auth::id()
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Entry updated successfully',
                    'ar_no' => $arNo,
                    'sequence' => intval($sequence)
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error updating plasma entry: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the plasma despense form.
     *
     * @return \Illuminate\View\View
     */
    public function despense()
    {
        try {
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

            \Log::info('Blood Centers Data for despense:', ['data' => $bloodCenters]);
            return view('factory.report.plasma_despense', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in despense: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading blood centers data');
        }
    }

    /**
     * Display the plasma rejection form.
     *
     * @return \Illuminate\View\View
     */
    public function rejection()
    {
        try {
            $bloodCenters = BagStatusDetail::getBloodCentres();
            return view('factory.plasma_management.plasma_rejection', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in rejection view: ' . $e->getMessage());
            return back()->with('error', 'Error loading plasma rejection view');
        }
    }

    /**
     * Get AR numbers for dropdown.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArNumbers(Request $request)
    {
        try {
            $search = $request->get('search');
            $query = PlasmaEntry::with('bloodBank')
                ->whereNotNull('alloted_ar_no');

            if ($search) {
                $query->where('alloted_ar_no', 'like', "%{$search}%");
            }

            $arNumbers = $query->select('id', 'alloted_ar_no', 'blood_bank_id', 'grn_no')
                ->orderBy('alloted_ar_no', 'desc')
                ->limit(10)
                ->get()
                ->map(function($entry) {
                    return [
                        'id' => $entry->alloted_ar_no,
                        'text' => $entry->alloted_ar_no,
                        'blood_bank_id' => $entry->blood_bank_id,
                        'grn_no' => $entry->grn_no
                    ];
                });

            return response()->json([
                'results' => $arNumbers
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching AR numbers: ' . $e->getMessage(), [
                'exception' => $e,
                'search' => $request->get('search')
            ]);

            return response()->json([
                'error' => 'Failed to fetch AR numbers',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plasma entry details by AR No.
     *
     * @param string $ar_no
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByArNo($ar_no)
    {
        try {
            $entry = PlasmaEntry::with('bloodBank')
                ->leftJoin('barcode_entries', 'plasma_entries.alloted_ar_no', '=', 'barcode_entries.ar_no')
                ->where('plasma_entries.alloted_ar_no', $ar_no)
                ->select('plasma_entries.*', 'barcode_entries.work_station')
                ->first();

            if (!$entry) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No entry found for the given AR No.'
                ], 404);
            }

            // Get all mega pool numbers for this AR No. that are not already used in bag_entries
            $megaPoolNumbers = DB::table('barcode_entries')
                ->where('ar_no', $ar_no)
                ->whereNotNull('mega_pool_no')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('bag_entries')
                        ->whereRaw('bag_entries.mega_pool_no = barcode_entries.mega_pool_no');
                })
                ->distinct()
                ->pluck('mega_pool_no')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'blood_bank_id' => $entry->blood_bank_id,
                    'blood_bank_name' => optional($entry->bloodBank)->name,
                    'blood_bank_city' => optional($entry->bloodBank->city)->name,
                    'grn_no' => $entry->grn_no,
                    'work_station' => $entry->work_station ?? '01', // Get from barcode_entries or default to '01'
                    'pickup_date' => $entry->pickup_date ? $entry->pickup_date->format('Y-m-d') : null,
                    'mega_pool_numbers' => $megaPoolNumbers // Array of all unused mega pool numbers
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching plasma entry by AR No: ' . $e->getMessage(), [
                'exception' => $e,
                'ar_no' => $ar_no
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch entry details'
            ], 500);
        }
    }

    public function getMiniPoolNumbers(Request $request)
    {
        try {
            $megaPoolNo = $request->get('mega_pool_no');
            
            if (!$megaPoolNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mega Pool Number is required'
                ], 400);
            }

            // Get mini pool numbers from barcode_entries table
            $barcodeEntry = DB::table('barcode_entries')
                ->where('mega_pool_no', $megaPoolNo)
                ->first();

            if (!$barcodeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No mini pool numbers found for the given Mega Pool Number'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'mini_pool_numbers' => $barcodeEntry->mini_pool_number
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching mini pool numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the sub mini pool entry form.
     *
     * @return \Illuminate\View\View
     */
    public function subMiniPoolEntry()
    {
        return view('factory.report.sub_minipool_entry');
    }

    public function getBagStatusDetails(Request $request)
    {
        try {
            $request->validate([
                'blood_bank_id' => 'required|exists:entities,id',
                'pickup_date' => 'required|date'
            ]);

            // Log the input parameters
            Log::info('Fetching bag status details with params:', [
                'blood_bank_id' => $request->blood_bank_id,
                'pickup_date' => $request->pickup_date
            ]);

            // Check each condition separately
            $bloodBankCount = DB::table('bag_status_details')
                ->where('blood_bank_id', $request->blood_bank_id)
                ->count();
            
            $dateCount = DB::table('bag_status_details')
                ->whereDate('pickup_date', $request->pickup_date)
                ->count();
            
            $statusCount = DB::table('bag_status_details')
                ->where('status', 'despense')
                ->count();

            Log::info('Individual condition counts:', [
                'blood_bank_count' => $bloodBankCount,
                'date_count' => $dateCount,
                'status_count' => $statusCount
            ]);

            // Get the actual records
            $bagStatusDetails = DB::table('bag_status_details')
                ->join('users', 'bag_status_details.created_by', '=', 'users.id')
                ->where('bag_status_details.blood_bank_id', $request->blood_bank_id)
                ->whereDate('bag_status_details.pickup_date', $request->pickup_date)
                ->where('bag_status_details.status', 'despense')
                ->select(
                    'bag_status_details.id',
                    'bag_status_details.mini_pool_id',
                    'bag_status_details.blood_bank_id',
                    'bag_status_details.pickup_date',
                    'bag_status_details.timestamp as despense_date',
                    'bag_status_details.created_by',
                    'users.name as created_by_name'
                )
                ->orderBy('bag_status_details.timestamp', 'desc')
                ->get();

            // Log the raw SQL query
            $query = DB::table('bag_status_details')
                ->where('blood_bank_id', $request->blood_bank_id)
                ->whereDate('pickup_date', $request->pickup_date)
                ->where('status', 'despense')
                ->toSql();
            
            Log::info('SQL Query:', [
                'query' => $query,
                'bindings' => [
                    'blood_bank_id' => $request->blood_bank_id,
                    'pickup_date' => $request->pickup_date,
                    'status' => 'despense'
                ]
            ]);

            Log::info('Retrieved bag status details:', ['data' => $bagStatusDetails]);

            return response()->json([
                'status' => 'success',
                'data' => $bagStatusDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching bag status details: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch bag status details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBagStatusForRejection(Request $request)
    {
        try {
            $request->validate([
                'blood_bank_id' => 'required|exists:entities,id',
                'pickup_date' => 'required|date'
            ]);

            Log::info('Fetching bag status for rejection with params:', [
                'blood_bank_id' => $request->blood_bank_id,
                'pickup_date' => $request->pickup_date
            ]);

            // First check if we have any records with the given conditions
            $count = DB::table('bag_status_details')
                ->where('blood_bank_id', $request->blood_bank_id)
                ->whereDate('pickup_date', $request->pickup_date)
                ->where('status', 'rejection')
                ->count();

            Log::info('Found records count:', ['count' => $count]);

            if ($count === 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No rejected records found for the selected criteria'
                ]);
            }

            // Build the query step by step
            $query = DB::table('bag_status_details')
                ->join('bag_entries_mini_pools', 'bag_entries_mini_pools.mini_pool_number', '=', 'bag_status_details.mini_pool_id')
                ->join('bag_entries', 'bag_entries.id', '=', 'bag_entries_mini_pools.bag_entries_id')
                ->join('bag_entries_details', 'bag_entries.id', '=', 'bag_entries_details.bag_entries_id')
                ->where('bag_status_details.blood_bank_id', $request->blood_bank_id)
                ->whereDate('bag_status_details.pickup_date', $request->pickup_date)
                ->where('bag_status_details.status', 'rejection')
                ->whereNotNull('bag_entries_details.donation_date')
                ->whereNotNull('bag_entries_details.blood_group')
                ->whereNotNull('bag_entries_details.bag_volume_ml')
                ->distinct();

            // Log the SQL query
            Log::info('SQL Query:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $bagStatusDetails = $query->select([
                'bag_status_details.mini_pool_id',
                'bag_entries_details.donation_date',
                'bag_entries_details.blood_group',
                'bag_entries_details.bag_volume_ml as bag_volume',
                'bag_entries.ar_no',
                'bag_status_details.status',
                'bag_status_details.created_by as rejected_by',
                'bag_status_details.timestamp as rejection_date'
            ])->get();

            Log::info('Retrieved bag status details for rejection:', [
                'count' => $bagStatusDetails->count(),
                'data' => $bagStatusDetails
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $bagStatusDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getBagStatusForRejection: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching bag status details: ' . $e->getMessage()
            ], 500);
        }
    }
} 