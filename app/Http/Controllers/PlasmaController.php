<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlasmaController extends Controller
{
    /**
     * Display the plasma dispensing form.
     *
     * @return \Illuminate\View\View
     */
    public function dispensing()
    {
        return view('plasma.dispensing');
    }

    /**
     * Store a plasma dispensing record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDispensing(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'batch_no' => 'required',
            'request_by' => 'required',
            'date' => 'required|date',
            'ar_no.*' => 'nullable',
            'pool_no.*' => 'nullable',
            'volume_requested.*' => 'nullable|numeric',
            'volume_issued.*' => 'nullable|numeric',
            'dispensed_by.*' => 'nullable',
            'verified_by.*' => 'nullable',
            'checked_by.*' => 'nullable',
        ]);

        // TODO: Add your storage logic here

        return redirect()->back()->with('success', 'Plasma dispensing record saved successfully.');
    }
} 