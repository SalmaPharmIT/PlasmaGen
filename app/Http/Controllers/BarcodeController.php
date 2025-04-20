<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    public function generate()
    {
        if (Auth::user()->role_id != 17) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }
        return view('barcode.generate');
    }

    public function generateCodes(Request $request)
    {
        if (Auth::user()->role_id != 17) {
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
        
        // Get the last serial number from the database or start from 0001
        $lastSerial = 1; // TODO: Get from database
        $serial = str_pad($lastSerial, 4, '0', STR_PAD_LEFT);

        // Generate mega pool number
        $megapool = "MG" . $year . $month . $workstation . $serial;

        // Generate mini pool numbers
        $minipools = [];
        for ($i = 1; $i <= 6; $i++) {
            $minipools[] = $year . $month . $workstation . $serial . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        return response()->json([
            'success' => true,
            'megapool' => $megapool,
            'minipools' => $minipools,
            'ar_number' => $request->ar_number,
            'ref_number' => $request->ref_number
        ]);
    }
} 