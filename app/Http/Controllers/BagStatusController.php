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

            $miniPoolDetails = BagStatusDetail::getNonReactiveMiniPoolDetails(
                $bloodCentreName,
                $pickupDate
            );

            return response()->json([
                'status' => 'success',
                'data' => $miniPoolDetails
            ]);
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
            $request->validate([
                'blood_bank' => 'required|exists:entities,id',
                'pickup_date' => 'required|date',
                'rejection_type' => 'required|array',
                'rejection_type.*' => 'required|in:damage,rejection,expiry,quality',
                'mini_pools' => 'required|array',
                'mini_pools.*.mini_pool_id' => 'required',
                'mini_pools.*.remarks' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Get or create plasma entry
            $plasmaEntry = DB::table('plasma_entries')
                ->where('blood_bank_id', $request->blood_bank)
                ->whereDate('pickup_date', $request->pickup_date)
                ->first();

            if (!$plasmaEntry) {
                // Create a new plasma entry
                $plasmaEntryId = DB::table('plasma_entries')->insertGetId([
                    'blood_bank_id' => $request->blood_bank,
                    'pickup_date' => $request->pickup_date,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $plasmaEntry = (object)['id' => $plasmaEntryId];
            }

            foreach ($request->mini_pools as $miniPool) {
                // Create bag status detail using mini pool ID directly from request
                BagStatusDetail::create([
                    'mini_pool_id' => $miniPool['mini_pool_id'],
                    'blood_bank_id' => $request->blood_bank,
                    'pickup_date' => $request->pickup_date,
                    'status' => 'rejection',
                    'timestamp' => now(),
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Plasma rejection records saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in storePlasmaRejection: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error saving plasma rejection records: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storePlasmaDespense(Request $request)
    {
        try {
            $request->validate([
                'blood_bank' => 'required|exists:entities,id',
                'pickup_date' => 'required|date',
                'mini_pools' => 'required|array',
                'mini_pools.*.mini_pool_id' => 'required',
                'mini_pools.*.remarks' => 'nullable|string'
            ]);

            DB::beginTransaction();

            // Get or create plasma entry
            $plasmaEntry = DB::table('plasma_entries')
                ->where('blood_bank_id', $request->blood_bank)
                ->whereDate('pickup_date', $request->pickup_date)
                ->first();

            if (!$plasmaEntry) {
                // Create a new plasma entry
                $plasmaEntryId = DB::table('plasma_entries')->insertGetId([
                    'blood_bank_id' => $request->blood_bank,
                    'pickup_date' => $request->pickup_date,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $plasmaEntry = (object)['id' => $plasmaEntryId];
            }

            foreach ($request->mini_pools as $miniPool) {
                // Get the barcode entry ID for this mini pool number
                $barcodeEntry = DB::table('bag_entries_mini_pools')
                    ->join('bag_entries', 'bag_entries.id', '=', 'bag_entries_mini_pools.bag_entries_id')
                    ->join('barcode_entries', 'barcode_entries.ar_no', '=', 'bag_entries.ar_no')
                    ->where('bag_entries_mini_pools.mini_pool_number', $miniPool['mini_pool_id'])
                    ->where('bag_entries.blood_bank_id', $request->blood_bank)
                    ->whereDate('bag_entries.pickup_date', $request->pickup_date)
                    ->select('barcode_entries.id')
                    ->first();

                if (!$barcodeEntry) {
                    throw new \Exception("No barcode entry found for mini pool number: {$miniPool['mini_pool_id']}");
                }

                BagStatusDetail::create([
                    'mini_pool_id' => $miniPool['mini_pool_id'],
                    'blood_bank_id' => $request->blood_bank,
                    'pickup_date' => $request->pickup_date,
                    'status' => 'despense',
                    'timestamp' => now(),
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Plasma despense records saved successfully'
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
} 