<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;

class BarcodeController extends Controller
{
    public function generate()
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get the reference number and number of workstations from entity settings
        $entityId = Auth::user()->entity_id;
        $entitySettings = \App\Models\EntitySetting::where('entity_id', $entityId)->first();
        $refNumber = $entitySettings ? $entitySettings->ref_no : '';
        $noOfWorkstations = $entitySettings ? $entitySettings->no_of_work_station : 0;

        return view('barcode.generate', compact('refNumber', 'noOfWorkstations'));
    }

    public function generateCodes(Request $request)
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'ar_number' => 'required',
            'ref_number' => 'required',
            'workstation_id' => 'required|numeric|min:1|max:99',
        ]);

        $year = date('y');
        $month = date('m');
        $workstation = str_pad($request->workstation_id, 2, '0', STR_PAD_LEFT);

        // Get the last mega pool number for the current year, month, and workstation
        $prefix = "MG" . $year . $month . $workstation;
        $lastMegaPool = \DB::table('barcode_entries')
            ->where('mega_pool_no', 'LIKE', $prefix . '%')
            ->orderBy('mega_pool_no', 'desc')
            ->first();

        // Extract the last sequence number or start from 1
        if ($lastMegaPool) {
            $lastSequence = intval(substr($lastMegaPool->mega_pool_no, -4));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        // Generate mega pool number
        $megapool = $prefix . $sequence;

        // Generate mini pool numbers
        $minipools = [];
        for ($i = 1; $i <= 6; $i++) {
            $minipools[] = $year . $month . $workstation . $sequence . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return response()->json([
            'success' => true,
            'megapool' => $megapool,
            'minipools' => $minipools,
            'ar_number' => $request->ar_number,
            'ref_number' => $request->ref_number
        ]);
    }

    public function saveBarcodes(Request $request)
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'workstation_id' => 'required',
            'ar_number' => 'required',
            'ref_number' => 'required',
            'mega_pool' => 'required',
            'mini_pools' => 'required|array'
        ]);

        try {
            $barcodeEntry = \DB::table('barcode_entries')->insert([
                'work_station' => $request->workstation_id,
                'ar_no' => $request->ar_number,
                'ref_doc_no' => $request->ref_number,
                'mega_pool_no' => $request->mega_pool,
                'mini_pool_number' => implode(',', $request->mini_pools),
                'timestamp' => now(),
                'created_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log the barcode generation in audit trail
            AuditTrail::log(
                'create',
                'Generate Barcode',
                'Barcode Generation',
                $request->mega_pool,
                [],
                [
                    'mega_pool_no' => $request->mega_pool,
                    'mini_pool_number' => implode(',', $request->mini_pools),
                    'ar_no' => $request->ar_number,
                    'ref_doc_no' => $request->ref_number,
                    'work_station' => $request->workstation_id
                ],
                'Generated barcode: ' . $request->mega_pool . ' with ' . count($request->mini_pools) . ' mini pools'
            );

            return response()->json([
                'success' => true,
                'message' => 'Barcodes saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving barcodes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getArNumbers()
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $arNumbers = \DB::table('plasma_entries')
            ->whereNotNull('alloted_ar_no')
            ->distinct()
            ->pluck('alloted_ar_no');

        return response()->json([
            'success' => true,
            'data' => $arNumbers
        ]);
    }

    public function reprintBarcodes(Request $request)
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'mega_pool' => 'required',
            'ar_number' => 'required',
            'ref_number' => 'required'
        ]);

        try {
            $barcodeEntry = \DB::table('barcode_entries')
                ->where('mega_pool_no', $request->mega_pool)
                ->first();

            if (!$barcodeEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'No barcodes found for the selected Mega Pool'
                ], 404);
            }

            // Get mini pools as array
            $miniPools = explode(',', $barcodeEntry->mini_pool_number);

            return response()->json([
                'success' => true,
                'ar_number' => $barcodeEntry->ar_no,
                'ref_number' => $barcodeEntry->ref_doc_no,
                'megapool' => $barcodeEntry->mega_pool_no,
                'minipools' => $miniPools
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barcodes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMegaPools()
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $megaPools = \DB::table('barcode_entries')
            ->select('mega_pool_no', 'ar_no', 'ref_doc_no')
            ->orderBy('mega_pool_no', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $megaPools
        ]);
    }

    public function generateTemplate(Request $request)
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $request->validate([
            'ar_number' => 'required',
            'ref_number' => 'required',
            'mega_pool' => 'required',
            'mini_pools' => 'required|array'
        ]);

        // Get mini pools from request
        $miniPools = $request->input('mini_pools', []);

        return view('barcode.template', [
            'ar_number' => $request->ar_number,
            'ref_number' => $request->ref_number,
            'mega_pool' => $request->mega_pool,
            'mini_pools' => $miniPools
        ]);
    }
}
