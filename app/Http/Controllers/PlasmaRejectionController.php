<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlasmaRejectionController extends Controller
{
    public function print(Request $request)
    {
        return view('factory.plasma_management.plasma_rejection_print', [
            'bloodCentre' => $request->bloodCentre,
            'date' => $request->date,
            'items' => $request->items
        ]);
    }
} 