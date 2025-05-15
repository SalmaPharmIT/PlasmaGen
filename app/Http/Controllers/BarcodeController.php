<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    public function generate()
    {
        if (!in_array(Auth::user()->role_id, [12, 17])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get the reference number from entity settings
        $entityId = Auth::user()->entity_id;
        $entitySettings = \App\Models\EntitySetting::where('entity_id', $entityId)->first();
        $refNumber = $entitySettings ? $entitySettings->ref_no : '';

        return view('barcode.generate', compact('refNumber'));
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
            \DB::table('barcode_entries')->insert([
                'work_station' => $request->workstation_id,
                'ar_no' => $request->ar_number,
                'ref_doc_no' => $request->ref_number,
                'mega_pool_no' => $request->mega_pool,
                'mini_pool_number' => implode(',', $request->mini_pools),
                'timestamp' => now(),
                'created_by' => Auth::user()->name,
                'created_at' => now(),
                'updated_at' => now()
            ]);

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
} 