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
     * Handle plasma release/reject submission
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
            public function submitPlasma(Request $request)
    {
        try {
            $items = $request->input('items', []);

            if (empty($items)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No items provided'
                ], 400);
            }

            DB::beginTransaction();

            foreach ($items as $item) {
                // Trim any whitespace from the input values
                $arNo = trim($item['arNo']);
                $megaPool = trim($item['megaPool']);
                $action = $item['action']; // 'release' or 'reject'

                try {
                    // Create a new record
                    $bagStatus = new BagStatusDetail();
                    $bagStatus->ar_no = $arNo;
                    $bagStatus->mini_pool_id = $megaPool;
                    $bagStatus->date = now()->format('Y-m-d');
                    $bagStatus->created_by = Auth::id();
                    $bagStatus->created_at = now();
                    $bagStatus->status_type = 'release'; // Set status_type to draft

                    // Set release and reject status based on action
                    if ($action === 'release') {
                        $bagStatus->release_status = 'approved'; // 1 for release
                        $bagStatus->reject_status = 'rejected';
                    } else if ($action === 'reject') {
                        $bagStatus->release_status = 'rejected';
                        $bagStatus->reject_status = 'approved'; // 1 for reject
                    }

                    $bagStatus->save();

                    Log::info('Created new bag status record:', [
                        'ar_no' => $arNo,
                        'mini_pool_id' => $megaPool,
                        'action' => $action,
                        'id' => $bagStatus->id
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create bag status record:', [
                        'ar_no' => $arNo,
                        'mini_pool_id' => $megaPool,
                        'error' => $e->getMessage()
                    ]);

                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Failed to create record for AR No: {$arNo} and Mega Pool: {$megaPool}. Error: " . $e->getMessage()
                    ], 500);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'All items processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
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

            // Handle request to get last AR sequence
            if ($request->has('get_last_ar_sequence')) {
                $year = $request->input('year', date('y'));

                // Get the last AR number for the specified year
                $lastArNo = DB::table('plasma_entries')
                    ->where('alloted_ar_no', 'LIKE', "%/{$year}/%")
                    ->whereNotNull('alloted_ar_no')
                    ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(alloted_ar_no, "/", -1) AS UNSIGNED)'), 'DESC')
                    ->first();

                $lastSequence = 0;
                if ($lastArNo && $lastArNo->alloted_ar_no) {
                    $parts = explode('/', $lastArNo->alloted_ar_no);
                    $lastSequence = intval(end($parts));
                }

                return response()->json([
                    'status' => 'success',
                    'last_sequence' => $lastSequence
                ]);
            }

            // Handle request to get last destruction sequence
            if ($request->has('get_last_sequence') && filter_var($request->is_rejection, FILTER_VALIDATE_BOOLEAN)) {
                $year = date('Y');

                // Get the last destruction number for this year
                $lastDestructionNo = DB::table('plasma_entries')
                    ->where('destruction_no', 'LIKE', "DES/{$year}/%")
                    ->whereNotNull('destruction_no')
                    ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(destruction_no, "/", -1) AS UNSIGNED)'), 'DESC')
                    ->first();

                $lastSequence = 0;
                if ($lastDestructionNo && $lastDestructionNo->destruction_no) {
                    $parts = explode('/', $lastDestructionNo->destruction_no);
                    $lastSequence = intval(end($parts));
                }

                return response()->json([
                    'status' => 'success',
                    'last_sequence' => $lastSequence
                ]);
            }

            // Handle bulk save
            if ($request->has('bulk_save')) {
                try {
                    $request->validate([
                        'entries' => 'required|array',
                        'entries.*.entry_id' => 'required|exists:plasma_entries,id',
                        'entries.*.remarks' => 'nullable|string'
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    Log::error('Validation error in bulk save:', [
                        'errors' => $e->errors(),
                        'request' => $request->all()
                    ]);
                    throw $e;
                }

                DB::beginTransaction();

                try {
                    // Separate entries into accepted and rejected
                    $acceptedEntries = [];
                    $rejectedEntries = [];

                    Log::info('Processing bulk save entries:', [
                        'count' => count($request->entries),
                        'entries' => $request->entries
                    ]);

                    foreach ($request->entries as $entry) {
                        if (isset($entry['is_rejection']) && filter_var($entry['is_rejection'], FILTER_VALIDATE_BOOLEAN)) {
                            $rejectedEntries[] = $entry;
                        } else {
                            $acceptedEntries[] = $entry;
                        }
                    }

                    Log::info('Entries separated:', [
                        'accepted_count' => count($acceptedEntries),
                        'rejected_count' => count($rejectedEntries)
                    ]);

                    // Process rejected entries
                    foreach ($rejectedEntries as $entry) {
                        try {
                            $plasmaEntry = PlasmaEntry::findOrFail($entry['entry_id']);

                            // Update with destruction number
                            $updateData = [
                                'destruction_no' => $entry['destruction_no'],
                                'remarks' => ($entry['remarks'] ? $entry['remarks'] . ' - ' : '') . 'Status: rejected',
                                'updated_by' => Auth::id()
                            ];

                            Log::info('Updating entry with rejection data:', [
                                'entry_id' => $entry['entry_id'],
                                'destruction_no' => $entry['destruction_no'],
                                'update_data' => $updateData
                            ]);

                            $plasmaEntry->update($updateData);

                            Log::info('Updated entry with destruction number:', [
                                'entry_id' => $entry['entry_id'],
                                'destruction_no' => $entry['destruction_no']
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error updating rejected entry:', [
                                'entry_id' => $entry['entry_id'] ?? 'unknown',
                                'exception' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e;
                        }
                    }

                    // Process accepted entries - now using a global sequence instead of per-blood-bank
                    if (count($acceptedEntries) > 0) {
                        // Get the last AR number for current year (global across all blood banks)
                        $year = date('y');

                        $lastArNo = DB::table('plasma_entries')
                            ->where('alloted_ar_no', 'LIKE', "%/{$year}/%")
                            ->whereNotNull('alloted_ar_no')
                            ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(alloted_ar_no, "/", -1) AS UNSIGNED)'), 'DESC')
                            ->lockForUpdate()
                            ->first();

                        $lastSequence = 0;
                        if ($lastArNo && $lastArNo->alloted_ar_no) {
                            $parts = explode('/', $lastArNo->alloted_ar_no);
                            $lastSequence = intval(end($parts));
                        }

                        // If client provided a current global sequence, use the higher value
                        if ($request->has('current_global_sequence')) {
                            $clientSequence = intval($request->current_global_sequence);
                            $lastSequence = max($lastSequence, $clientSequence);
                        }

                        Log::info('Last global AR sequence:', [
                            'last_sequence' => $lastSequence,
                            'client_sequence' => $request->current_global_sequence ?? 'not provided'
                        ]);

                        // Process each accepted entry with a global sequence
                        foreach ($acceptedEntries as $entry) {
                            try {
                                $plasmaEntry = PlasmaEntry::findOrFail($entry['entry_id']);
                                $bloodBankId = $plasmaEntry->blood_bank_id;

                                // Skip entries that already have an AR number assigned
                                if ($plasmaEntry->alloted_ar_no) {
                                    Log::info('Entry already has an AR number, skipping:', [
                                        'entry_id' => $entry['entry_id'],
                                        'existing_ar_no' => $plasmaEntry->alloted_ar_no
                                    ]);
                                    continue;
                                }

                                // Format blood bank ID to 4 digits
                                $bloodBankCode = str_pad($bloodBankId, 4, '0', STR_PAD_LEFT);

                                // If client provided an AR number, validate and use it
                                if (isset($entry['ar_no']) && !empty($entry['ar_no'])) {
                                    $arNo = $entry['ar_no'];

                                    // Verify AR number doesn't exist
                                    $exists = PlasmaEntry::where('alloted_ar_no', $arNo)
                                        ->where('id', '!=', $entry['entry_id'])
                                        ->exists();

                                    if ($exists) {
                                        Log::warning('AR Number already exists, generating a new one:', [
                                            'conflicting_ar_no' => $arNo
                                        ]);

                                        // Increment sequence
                                        $lastSequence++;
                                        $sequence = str_pad($lastSequence, 4, '0', STR_PAD_LEFT);

                                        // Generate new AR number
                                        $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";

                                        // Make sure it's unique
                                        while (PlasmaEntry::where('alloted_ar_no', $arNo)->exists()) {
                                            $lastSequence++;
                                            $sequence = str_pad($lastSequence, 4, '0', STR_PAD_LEFT);
                                            $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";
                                        }
                                    }
                                } else {
                                    // Increment sequence number
                                    $lastSequence++;
                                    $sequence = str_pad($lastSequence, 4, '0', STR_PAD_LEFT);

                                    // Generate AR number
                                    $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";

                                    // Verify AR number doesn't exist
                                    $exists = PlasmaEntry::where('alloted_ar_no', $arNo)->exists();
                                    if ($exists) {
                                        Log::warning('Generated AR Number already exists, trying next number:', [
                                            'conflicting_ar_no' => $arNo
                                        ]);

                                        // Try to find a unique number
                                        while (PlasmaEntry::where('alloted_ar_no', $arNo)->exists()) {
                                            $lastSequence++;
                                            $sequence = str_pad($lastSequence, 4, '0', STR_PAD_LEFT);
                                            $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$sequence}";
                                        }
                                    }
                                }

                                // Update the entry
                                $updateData = [
                                    'alloted_ar_no' => $arNo,
                                    'remarks' => $entry['remarks'],
                                    'status' => $entry['status'],
                                    'updated_by' => Auth::id()
                                ];

                                Log::info('Updating entry with AR number:', [
                                    'entry_id' => $entry['entry_id'],
                                    'ar_no' => $arNo,
                                    'update_data' => $updateData
                                ]);

                                $plasmaEntry->update($updateData);
                            } catch (\Exception $e) {
                                Log::error('Error processing AR number for entry:', [
                                    'entry_id' => $entry['entry_id'] ?? 'unknown',
                                    'exception' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                throw $e;
                            }
                        }
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'All changes have been saved successfully',
                        'last_sequence' => $lastSequence ?? 0
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
                'generate_only' => 'nullable|boolean',
                'preview_ar_no' => 'nullable|string',
                'blood_bank_id' => 'required_if:is_rejection,false|exists:entities,id',
                'current_sequence' => 'nullable|integer',
                'destruction_no' => 'nullable|string',
                'preview_destruction_no' => 'nullable|string',
                'is_rejection' => 'required|in:true,false,1,0,"true","false"',
                'get_last_sequence' => 'nullable|boolean'
            ]);

            DB::beginTransaction();

            try {
                $entry = PlasmaEntry::findOrFail($request->entry_id);

                // If this is a rejection, update with destruction number
                if (filter_var($request->is_rejection, FILTER_VALIDATE_BOOLEAN)) {
                    // Generate destruction number if not provided
                    $destructionNo = $this->generateDestructionNumber($request->preview_destruction_no, $request->current_sequence);

                    // If generate_only is true, just return the destruction number without saving
                    if ($request->boolean('generate_only')) {
                        DB::commit();
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Destruction number generated successfully',
                            'destruction_no' => $destructionNo
                        ]);
                    }

                    $entry->update([
                        'destruction_no' => $destructionNo,
                        'remarks' => ($request->remarks ? $request->remarks . ' - ' : '') . 'Status: rejected',
                        'updated_by' => Auth::id()
                    ]);

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Entry rejected successfully',
                        'destruction_no' => $destructionNo
                    ]);
                }

                $bloodBankId = $request->blood_bank_id;
                $bloodBankCode = str_pad($bloodBankId, 4, '0', STR_PAD_LEFT);
                $year = date('y');

                // Get the last AR number for ANY blood bank and current year (global sequence)
                $lastArNo = DB::table('plasma_entries')
                    ->where('alloted_ar_no', 'LIKE', "%/{$year}/%")
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
                    Log::warning('AR Number already exists, generating a new one:', [
                        'conflicting_ar_no' => $arNo
                    ]);

                    // Try to find a unique number
                    $newSequence = intval($sequence) + 1;
                    $newSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
                    $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$newSequence}";

                    // Make sure it's unique
                    while (PlasmaEntry::where('alloted_ar_no', $arNo)->exists()) {
                        $newSequence = intval($newSequence) + 1;
                        $newSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
                        $arNo = "AR/RM10001/{$bloodBankCode}/{$year}/{$newSequence}";
                    }

                    // Update the sequence to the new value
                    $sequence = $newSequence;
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
     * Display the plasma despense form.
     *
     * @return \Illuminate\View\View
     */
    public function release(Request $request)
    {
        try {
            $query = DB::table('barcode_entries as be')
                ->select('be.ar_no',
                    DB::raw('GROUP_CONCAT(CONCAT(be.mega_pool_no, "::", ntr.status) ORDER BY be.mega_pool_no) as mega_pool_data'))
                ->distinct()
                ->join('nat_test_report as ntr', 'be.mega_pool_no', '=', 'ntr.mini_pool_id')
                ->groupBy('be.ar_no');

            // Get paginated results
            $perPage = 20; // Number of unique AR numbers per page
            $page = $request->input('page', 1);

            $totalRecords = $query->get()->count();
            $results = $query->orderBy('be.ar_no')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get()
                ->map(function($item) {
                    $megaPoolData = explode(',', $item->mega_pool_data);
                    $megaPools = array_map(function($data) {
                        list($poolNo, $status) = explode('::', $data);
                        return [
                            'pool_no' => $poolNo,
                            'status' => $status
                        ];
                    }, $megaPoolData);

                    return [
                        'ar_no' => $item->ar_no,
                        'mega_pools' => $megaPools,
                        'rowspan' => count($megaPools) // Add rowspan for merging
                    ];
                });

            if ($request->ajax()) {
                return response()->json([
                    'data' => $results,
                    'pagination' => [
                        'total' => $totalRecords,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($totalRecords / $perPage)
                    ]
                ]);
            }

            // Initial data for first page
            return view('factory.report.plasma_release', [
                'initialData' => $results,
                'pagination' => [
                    'total' => $totalRecords,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($totalRecords / $perPage)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in plasma release view: ' . $e->getMessage());
            return $request->ajax()
                ? response()->json(['error' => 'Error loading data'], 500)
                : back()->with('error', 'Error loading plasma release view');
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
            return view('factory.report.plasma_rejection', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in rejection view: ' . $e->getMessage());
            return back()->with('error', 'Error loading plasma rejection view');
        }
    }

    /**
     * Display the plasma rejection list.
     *
     * @return \Illuminate\View\View
     */
    public function rejectionList()
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

            return view('factory.plasma_management.plasma_rejection', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in rejection list view: ' . $e->getMessage());
            return back()->with('error', 'Error loading plasma rejection list view');
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
                        'grn_no' => $entry->grn_no,
                        'pick_up_date' => $entry->pickup_date,
                        'receipt_date' => $entry->receipt_date
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
            Log::info('Fetching plasma entry by AR No:', ['ar_no' => $ar_no]);

            $entry = PlasmaEntry::with('bloodBank')
                ->leftJoin('barcode_entries', 'plasma_entries.alloted_ar_no', '=', 'barcode_entries.ar_no')
                ->where('plasma_entries.alloted_ar_no', $ar_no)
                ->select('plasma_entries.*', 'barcode_entries.work_station')
                ->first();

            Log::info('Query result:', ['entry' => $entry ? $entry->toArray() : null]);

            if (!$entry) {
                Log::warning('No plasma entry found for AR No:', ['ar_no' => $ar_no]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'No plasma entry found for the given AR No.'
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

    /**
     * Display the AR Number List.
     *
     * @return \Illuminate\View\View
     */
    public function arList()
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

            return view('factory.plasma_management.ar_list', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in arList: ' . $e->getMessage());
            return back()->with('error', 'Error loading AR Number List');
        }
    }

    /**
     * Display the Destruction List.
     *
     * @return \Illuminate\View\View
     */
    public function destructionList()
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

            return view('factory.plasma_management.destruction_list', compact('bloodCenters'));
        } catch (\Exception $e) {
            \Log::error('Error in destructionList: ' . $e->getMessage());
            return back()->with('error', 'Error loading Destruction List');
        }
    }

    /**
     * Get AR list data for the table.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArList(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            $isExport = $request->boolean('export', false);

            $query = PlasmaEntry::with(['bloodBank', 'creator'])
                ->whereNotNull('alloted_ar_no')
                ->orderBy('created_at', 'desc');

            $total = $query->count();

            // If exporting, get all records
            if ($isExport) {
                $entries = $query->get();
            } else {
                $entries = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
            }

            $entries = $entries->map(function($entry) {
                return [
                    'pickup_date' => $entry->pickup_date ? $entry->pickup_date->format('Y-m-d') : null,
                    'receipt_date' => $entry->reciept_date ? $entry->reciept_date->format('Y-m-d') : null,
                    'grn_no' => $entry->grn_no,
                    'blood_bank_name' => $entry->bloodBank ? $entry->bloodBank->name : null,
                    'plasma_qty' => $entry->plasma_qty,
                    'ar_no' => $entry->alloted_ar_no,
                    'entered_by' => $entry->creator ? $entry->creator->name : null,
                    'remarks' => $entry->remarks
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $entries,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching AR list: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch AR list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get destruction list data for the table.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDestructionList(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $page = $request->input('page', 1);
            $isExport = $request->boolean('export', false);

            $query = PlasmaEntry::with(['bloodBank', 'creator'])
                ->leftJoin(DB::raw('(SELECT destruction_no, MIN(total_bag_val) as total_bag_val
                                   FROM plasma_entries_destruction
                                   GROUP BY destruction_no) as ped'),
                    'plasma_entries.destruction_no', '=', 'ped.destruction_no')
                ->whereNotNull('plasma_entries.destruction_no')
                ->select('plasma_entries.*', 'ped.total_bag_val')
                ->orderBy('plasma_entries.created_at', 'desc');

            $total = $query->count();

            // If exporting, get all records
            if ($isExport) {
                $entries = $query->get();
            } else {
                $entries = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
            }

            $entries = $entries->map(function($entry) {
                return [
                    'pickup_date' => $entry->pickup_date ? $entry->pickup_date->format('Y-m-d') : null,
                    'receipt_date' => $entry->reciept_date ? $entry->reciept_date->format('Y-m-d') : null,
                    'grn_no' => $entry->grn_no,
                    'blood_bank_name' => $entry->bloodBank ? $entry->bloodBank->name : null,
                    'plasma_qty' => $entry->plasma_qty,
                    'destruction_no' => $entry->destruction_no,
                    'entered_by' => $entry->creator ? $entry->creator->name : null,
                    'remarks' => $entry->remarks,
                    'total_bag_val' => $entry->total_bag_val
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $entries,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching destruction list: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch destruction list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the print view for AR Number List.
     *
     * @return \Illuminate\View\View
     */
    public function printArList()
    {
        return view('factory.plasma_management.ar_list_print');
    }

    /**
     * Display the print view for Destruction List.
     *
     * @return \Illuminate\View\View
     */
    public function printDestructionList()
    {
        return view('factory.plasma_management.destruction_list_print');
    }

    /**
     * Display the plasma dispensing print template.
     *
     * @return \Illuminate\View\View
     */
    public function printDispensing()
    {
        return view('factory.plasma_management.plasma_dispensing_print');
    }

    /**
     * Reject a mega pool and store rejection data in plasma_entries.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectMegaPool(Request $request)
    {
        try {
            Log::info('Starting mega pool rejection process:', [
                'mega_pool_no' => $request->mega_pool_no,
                'ar_no' => $request->ar_no,
                'reject_reason' => $request->reject_reason,
                'remarks' => $request->remarks
            ]);

            $request->validate([
                'mega_pool_no' => 'required|string',
                'ar_no' => 'required|string',
                'reject_reason' => 'required|in:Short Tail,Damage,Red',
                'remarks' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // First check if the plasma entry exists at all
            $plasmaEntry = PlasmaEntry::where('alloted_ar_no', $request->ar_no)->first();

            if (!$plasmaEntry) {
                Log::warning('Plasma entry not found:', ['ar_no' => $request->ar_no]);
                throw new \Exception('No plasma entry found for the given AR number. Please verify the AR number is correct.');
            }

            // Then check if it's already been rejected
            if ($plasmaEntry->destruction_no) {
                Log::warning('Plasma entry already rejected:', [
                    'ar_no' => $request->ar_no,
                    'destruction_no' => $plasmaEntry->destruction_no
                ]);
                throw new \Exception('This AR number has already been rejected with destruction number: ' . $plasmaEntry->destruction_no);
            }

            // Generate destruction number using the helper function
            $destructionNo = $this->generateDestructionNumber();

            Log::info('Generated destruction number:', ['destruction_no' => $destructionNo]);

            // Update the plasma entry with rejection details
            $updateData = [
                'destruction_no' => $destructionNo,
                'reject_reason' => $request->reject_reason,
                'remarks' => ($request->remarks ? $request->remarks . ' - ' : '') . 'Status: rejected',
                'updated_by' => Auth::id()
            ];

            Log::info('Updating plasma entry with data:', $updateData);

            $plasmaEntry->update($updateData);

            Log::info('Successfully updated plasma entry with rejection details:', [
                'ar_no' => $request->ar_no,
                'destruction_no' => $destructionNo,
                'reject_reason' => $request->reject_reason
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mega pool rejected successfully',
                'destruction_no' => $destructionNo,
                'reject_reason' => $request->reject_reason
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting mega pool: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate destruction number with proper format DES/YYYY/NNN
     * where DES is Destruction, YYYY is the full year, and NNN is a 3-digit sequence starting from 001
     */
    private function generateDestructionNumber($previewDestructionNo = null, $currentSequence = null)
    {
        try {
            // Get full 4-digit year (not 2 digits)
            $year = date('Y');

            // Get the last destruction number for this year
            $lastDestructionNo = DB::table('plasma_entries')
                ->where('destruction_no', 'LIKE', "DES/{$year}/%")
                ->whereNotNull('destruction_no')
                ->orderBy(DB::raw('CAST(SUBSTRING_INDEX(destruction_no, "/", -1) AS UNSIGNED)'), 'DESC')
                ->lockForUpdate()
                ->first();

            $lastSequence = 0;
            if ($lastDestructionNo && $lastDestructionNo->destruction_no) {
                // Extract the last sequence number from the destruction number
                $parts = explode('/', $lastDestructionNo->destruction_no);
                $lastSequence = intval(end($parts));
            }

            // If a current sequence is provided from client, use the higher value
            $sequence = $lastSequence + 1;
            if ($currentSequence !== null && is_numeric($currentSequence)) {
                $sequence = max($sequence, intval($currentSequence));
            }

            // Format sequence number to 3 digits
            $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);

            // Generate the destruction number in the format DES/YYYY/NNN
            $destructionNo = "DES/{$year}/{$formattedSequence}";

            // If a preview destruction number was provided and is valid, use it if it's higher
            if ($previewDestructionNo && preg_match("/^DES\/{$year}\/(\d{3})$/", $previewDestructionNo, $matches)) {
                $previewSequence = intval($matches[1]);
                if ($previewSequence >= $sequence) {
                    $destructionNo = $previewDestructionNo;
                    $sequence = $previewSequence;
                    $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);
                }
            }

            // Verify this destruction number doesn't exist (double-check)
            $exists = DB::table('plasma_entries')
                ->where('destruction_no', $destructionNo)
                ->exists();

            if ($exists) {
                // If it exists, increment and try again
                $sequence++;
                $formattedSequence = str_pad($sequence, 3, '0', STR_PAD_LEFT);
                $destructionNo = "DES/{$year}/{$formattedSequence}";

                // Check again
                $exists = DB::table('plasma_entries')
                    ->where('destruction_no', $destructionNo)
                    ->exists();

                if ($exists) {
                    throw new \Exception("Destruction Number {$destructionNo} already exists. Please try again.");
                }
            }

            return $destructionNo;

        } catch (\Exception $e) {
            Log::error('Error generating destruction number: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Get AR details including Blood Centre, Date, and Pickup Date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArDetails(Request $request)
    {
        try {
            $arNumber = $request->input('ar_number');

            if (!$arNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'A.R. Number is required'
                ], 400);
            }

            $entry = PlasmaEntry::with('bloodBank.city', 'bloodBank.state')
                ->where('alloted_ar_no', $arNumber)
                ->first();

            if (!$entry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified A.R. Number'
                ], 404);
            }

            $bloodBankName = $entry->bloodBank ? $entry->bloodBank->name : '';
            $cityName = $entry->bloodBank && $entry->bloodBank->city ? $entry->bloodBank->city->name : '';
            $stateName = $entry->bloodBank && $entry->bloodBank->state ? $entry->bloodBank->state->name : '';

            $bloodCentre = $bloodBankName;
            if ($cityName) {
                $bloodCentre .= ', ' . $cityName;
            }
            if ($stateName) {
                $bloodCentre .= ', ' . $stateName;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'blood_centre' => $bloodCentre,
                    'date' => $entry->reciept_date ? $entry->reciept_date->format('Y-m-d') : null,
                    'pickup_date' => $entry->pickup_date ? $entry->pickup_date->format('Y-m-d') : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching AR details: ' . $e->getMessage(), [
                'exception' => $e,
                'ar_number' => $request->input('ar_number')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch AR details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quality rejected plasma entries by AR number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQualityRejectedEntries(Request $request)
    {
        try {
            $arNumber = $request->get('ar_number');

            if (!$arNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'AR Number is required'
                ], 400);
            }

            // Check if we need to create a test entry (this is for development only)
            $createTest = $request->has('create_test');
            if ($createTest) {
                $this->createTestQualityRejectedEntry($arNumber);
            }

            // Fetch all entries from plasma_entries_destruction where reject_reason is 'quality-rejected'
            $entries = DB::table('plasma_entries_destruction')
                ->select(
                    'id',
                    'mega_pool_id',
                    'donor_id',
                    'donation_date',
                    'blood_group',
                    'bag_volume_ml as bag_volume',
                    'reject_reason'
                )
                ->where('ar_no', $arNumber)
                ->where('reject_reason', 'quality-rejected')
                ->get();

            // For each entry, if it has a mega_pool_id, try to get more complete information
            // from the bag_entries_details table
            foreach ($entries as $entry) {
                if ($entry->mega_pool_id) {
                    // Find the bag entry by mega_pool_no
                    $bagEntry = DB::table('bag_entries')
                        ->where('mega_pool_no', $entry->mega_pool_id)
                        ->first();

                    if ($bagEntry) {
                        // Get the first bag entry detail
                        $bagEntryDetail = DB::table('bag_entries_details')
                            ->where('bag_entries_id', $bagEntry->id)
                            ->first();

                        if ($bagEntryDetail) {
                            // Update entry with more complete information if available
                            $entry->donation_date = $bagEntryDetail->donation_date ?? $entry->donation_date;
                            $entry->blood_group = $bagEntryDetail->blood_group ?? $entry->blood_group;
                            $entry->bag_volume = $bagEntryDetail->bag_volume_ml ?? $entry->bag_volume;

                            \Log::info('Updated entry with bag details:', [
                                'id' => $entry->id,
                                'mega_pool_id' => $entry->mega_pool_id,
                                'donation_date' => $entry->donation_date,
                                'blood_group' => $entry->blood_group,
                                'bag_volume' => $entry->bag_volume
                            ]);
                        }
                    }
                }

                // Log each entry's details
                \Log::info('Entry details:', [
                    'id' => $entry->id,
                    'donor_id' => $entry->donor_id,
                    'donation_date' => $entry->donation_date,
                    'blood_group' => $entry->blood_group,
                    'bag_volume' => $entry->bag_volume,
                    'reject_reason' => $entry->reject_reason
                ]);
            }

            // Add some dummy data to test UI rendering if there are no entries
            if ($entries->isEmpty()) {
                $dummyEntry = (object)[
                    'id' => 999,
                    'donor_id' => 'TEST-123',
                    'donation_date' => date('Y-m-d'),
                    'blood_group' => 'A+',
                    'bag_volume' => 250,
                    'reject_reason' => 'quality-rejected'
                ];

                $entries = collect([$dummyEntry]);

                \Log::info('No entries found, adding dummy data for testing');
            }

            \Log::info('Quality rejected entries found:', [
                'ar_number' => $arNumber,
                'count' => $entries->count(),
                'entries' => json_encode($entries)
            ]);

            return response()->json([
                'success' => true,
                'data' => $entries
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getQualityRejectedEntries: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching quality rejected entries: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get plasma entry details by AR No.
     *
     * @param string $ar_no
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReleaseArNo($ar_no)
    {
        try {
            Log::info('Fetching data for AR No:', ['ar_no' => $ar_no]);

            // Get bag status details where status_type is 'final'
            try {
                $finalStatusDetails = DB::table('bag_status_details')
                    ->where('ar_no', $ar_no)
                    ->where('status_type', 'final')
                    ->get();

                // If we need to filter out duplicates, do it by mega_pool_no
                if ($finalStatusDetails->count() > 0) {
                    $uniqueDetails = [];
                    $processedMegaPools = [];

                    foreach ($finalStatusDetails as $detail) {
                        $megaPoolNo = $detail->mega_pool_no ?? '';

                        if (empty($megaPoolNo) || !in_array($megaPoolNo, $processedMegaPools)) {
                            $uniqueDetails[] = $detail;
                            if (!empty($megaPoolNo)) {
                                $processedMegaPools[] = $megaPoolNo;
                            }
                        }
                    }

                    $finalStatusDetails = collect($uniqueDetails);
                }

                Log::info('Final status details for AR No: ' . $ar_no, [
                    'count' => $finalStatusDetails->count()
                ]);
            } catch (\Exception $e) {
                Log::error('Error fetching bag status details: ' . $e->getMessage());
                $finalStatusDetails = collect([]);
            }

            // Check if we have any results
            if ($finalStatusDetails->count() == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No records found with status_type "final" for the given AR No.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'final_status_details' => $finalStatusDetails // Data from bag_status_details with status_type 'final'
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
    /**
     * Get all batch numbers from the bag_status_details table.
     * If a specific batch number is provided, filter by that batch number.
     *
     * @param string|null $batch_number Optional batch number to filter by
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBatchNumbers($batch_number = null)
    {
        try {
            Log::info('Fetching batch numbers from bag_status_details', [
                'filter_batch_number' => $batch_number
            ]);

            // If batch_number is "all" or null, return all batch numbers for the dropdown
            if ($batch_number === 'all' || $batch_number === null) {
                $query = DB::table('bag_status_details')
                    ->select('batch_no')
                    ->whereNotNull('batch_no')
                    ->distinct()
                    ->orderBy('batch_no');

                $batchNumbers = $query->get();

                Log::info('Retrieved batch numbers', [
                    'count' => $batchNumbers->count(),
                    'batch_numbers' => $batchNumbers->pluck('batch_no')
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $batchNumbers
                ]);
            }
            // If a specific batch number is provided, fetch detailed data for that batch
            else {
                // Join with other relevant tables to get complete data
                $batchDetails = DB::table('bag_status_details as bsd')
                    ->select(
                        'bsd.id',
                        'bsd.mini_pool_id',
                        'bsd.ar_no',
                        'bsd.batch_no',
                        'bsd.date',
                        'bsd.status',
                        'bsd.status_type',
                        'bsd.issued_volume',
                        'bsd.total_volume',
                        'bsd.total_issued_volume',
                        'bsd.created_by',
                        'u.name as created_by_name',
                        'e.name as blood_bank_name'
                    )
                    ->leftJoin('users as u', 'bsd.created_by', '=', 'u.id')
                    ->leftJoin('entities as e', 'bsd.blood_bank_id', '=', 'e.id')
                    ->where('bsd.batch_no', $batch_number)
                    ->orderBy('bsd.date', 'desc')
                    ->get();

                Log::info('Retrieved batch details', [
                    'batch_number' => $batch_number,
                    'count' => $batchDetails->count(),
                    'details' => $batchDetails
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'batch_details' => $batchDetails
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching batch data: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch batch data: ' . $e->getMessage()
            ], 500);
        }
    }
}
