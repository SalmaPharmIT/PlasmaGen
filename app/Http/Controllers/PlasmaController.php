<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\PlasmaEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlasmaController extends Controller
{
    /**
     * Display the plasma dispensing form.
     *
     * @return \Illuminate\View\View
     */
    public function dispensing()
    {
        return view('factory.plasma_management.plasma_dispensing');
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
                'receipt_date.*' => 'nullable|date',
                'grn_no.*' => 'nullable|string',
                'blood_bank.*' => 'nullable|exists:entities,id',
                'ar_no.*' => 'nullable|string',
                'remarks.*' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $userId = Auth::id();
            $entries = [];

            foreach ($request->receipt_date as $index => $date) {
                // Skip empty rows
                if (empty($date) && empty($request->grn_no[$index]) && empty($request->blood_bank[$index]) && 
                    empty($request->ar_no[$index]) && empty($request->remarks[$index])) {
                    continue;
                }

                $entry = PlasmaEntry::create([
                    'reciept_date' => $date,
                    'grn_no' => $request->grn_no[$index],
                    'blood_bank_id' => $request->blood_bank[$index],
                    'alloted_ar_no' => $request->ar_no[$index],
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
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($entry) {
                return [
                    'id' => $entry->id,
                    'receipt_date' => $entry->reciept_date ? $entry->reciept_date->format('Y-m-d') : null,
                    'grn_no' => $entry->grn_no,
                    'blood_bank' => $entry->bloodBank ? $entry->bloodBank->name : null,
                    'blood_bank_id' => $entry->blood_bank_id,
                    'ar_no' => $entry->alloted_ar_no,
                    'destruction_no' => $entry->destruction_no,
                    'entered_by' => $entry->creator ? $entry->creator->name : null,
                    'remarks' => $entry->remarks,
                    'status' => $entry->status ?? 'pending'  // Add status field if needed
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
                    'entries.*.destruction_no' => 'nullable|string',
                    'entries.*.remarks' => 'nullable|string',
                    'entries.*.status' => 'required|in:accepted,rejected',
                    'entries.*.ar_no' => 'required_if:entries.*.status,accepted|string'
                ]);

                DB::beginTransaction();

                foreach ($request->entries as $entry) {
                    $plasmaEntry = PlasmaEntry::findOrFail($entry['entry_id']);
                    
                    if ($entry['status'] === 'accepted') {
                        $plasmaEntry->update([
                            'alloted_ar_no' => $entry['ar_no'],
                            'destruction_no' => $entry['destruction_no'],
                            'remarks' => $entry['remarks'],
                            'status' => $entry['status'],
                            'updated_by' => Auth::id()
                        ]);
                    } else {
                        $plasmaEntry->update([
                            'destruction_no' => $entry['destruction_no'],
                            'remarks' => $entry['remarks'],
                            'status' => $entry['status'],
                            'updated_by' => Auth::id()
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'All entries updated successfully'
                ]);
            }

            // Handle single entry
            $request->validate([
                'entry_id' => 'required|exists:plasma_entries,id',
                'destruction_no' => 'nullable|string',
                'remarks' => 'nullable|string',
                'status' => 'required|in:accepted,rejected',
                'generate_only' => 'nullable|boolean'
            ]);

            $entry = PlasmaEntry::findOrFail($request->entry_id);
            
            Log::info('Found plasma entry:', [
                'entry' => $entry->toArray()
            ]);

            if ($request->status === 'accepted') {
                // Get the blood bank ID and pad it to 4 digits
                $bloodBankId = str_pad($entry->blood_bank_id, 4, '0', STR_PAD_LEFT);
                $lastFourDigits = substr($bloodBankId, -4);

                // Get current year's last 2 digits
                $year = date('y');

                // Get the last AR number for this year with proper LIKE pattern
                $pattern = "AR/RM10001/{$lastFourDigits}/{$year}/%";
                $lastArNo = PlasmaEntry::where('alloted_ar_no', 'LIKE', $pattern)
                    ->whereNotNull('alloted_ar_no')
                    ->orderByRaw('CAST(SUBSTRING(alloted_ar_no, -4) AS UNSIGNED) DESC')
                    ->first();

                Log::info('AR Number Generation Data:', [
                    'bloodBankId' => $bloodBankId,
                    'lastFourDigits' => $lastFourDigits,
                    'year' => $year,
                    'pattern' => $pattern,
                    'lastArNo' => $lastArNo ? $lastArNo->alloted_ar_no : null
                ]);

                // Generate sequential number
                $sequence = '0001';
                if ($lastArNo && $lastArNo->alloted_ar_no) {
                    // Extract the last 4 digits
                    if (preg_match('/(\d{4})$/', $lastArNo->alloted_ar_no, $matches)) {
                        $lastSequence = intval($matches[1]);
                        $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                    }
                }

                // Generate the AR number
                $arNo = "AR/RM10001/{$lastFourDigits}/{$year}/{$sequence}";

                Log::info('Generated AR Number:', [
                    'arNo' => $arNo,
                    'sequence' => $sequence,
                    'generate_only' => (bool)$request->generate_only
                ]);

                // If generate_only is true, just return the AR number without saving
                if ((bool)$request->generate_only) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'AR number generated successfully',
                        'ar_no' => $arNo
                    ]);
                }

                // Update the entry
                $entry->update([
                    'alloted_ar_no' => $arNo,
                    'destruction_no' => $request->destruction_no,
                    'remarks' => $request->remarks,
                    'status' => $request->status,
                    'updated_by' => Auth::id()
                ]);
            } else {
                // For rejected entries
                // Get current year's last 2 digits
                $year = date('y');
                
                // Get the last destruction number for this year
                $lastDestructionNo = PlasmaEntry::where('destruction_no', 'like', "DES/{$year}/%")
                    ->orderBy('destruction_no', 'desc')
                    ->first();

                // Generate sequential number
                $sequence = '0001';
                if ($lastDestructionNo) {
                    $lastSequence = intval(substr($lastDestructionNo->destruction_no, -4));
                    $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                }

                // Generate the destruction number
                $destructionNo = "DES/{$year}/{$sequence}";

                Log::info('Generated Destruction Number:', [
                    'destructionNo' => $destructionNo,
                    'year' => $year,
                    'sequence' => $sequence
                ]);

                // Update the entry with the generated destruction number
                $entry->update([
                    'destruction_no' => $destructionNo,
                    'remarks' => $request->remarks,
                    'status' => $request->status,
                    'updated_by' => Auth::id()
                ]);

                // Return specific response for rejected entries
                return response()->json([
                    'status' => 'success',
                    'message' => 'Entry rejected successfully',
                    'entry_id' => $entry->id,
                    'entry_status' => 'rejected',
                    'destruction_no' => $destructionNo
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Entry updated successfully',
                'ar_no' => $entry->alloted_ar_no,
                'destruction_no' => $entry->destruction_no
            ]);

        } catch (\Exception $e) {
            if (isset($transaction)) {
                DB::rollBack();
            }
            Log::error('Error updating plasma entry: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update entry',
                'error' => $e->getMessage()
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
        return view('factory.report.plasma_despense');
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
                ->where('alloted_ar_no', $ar_no)
                ->first();

            if (!$entry) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No entry found for the given AR No.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'blood_bank_id' => $entry->blood_bank_id,
                    'blood_bank_name' => optional($entry->bloodBank)->name,
                    'blood_bank_city' => optional($entry->bloodBank->city)->name,
                    'grn_no' => $entry->grn_no
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
} 