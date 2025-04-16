<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BagEntryController extends Controller
{
    /**
     * Show the users list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('newbagentry.bag_entry');
    }

    /**
     * Store a newly created bag entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'blood_centre' => 'required|string|max:255',
            'work_station' => 'required|string|max:255',
            'date' => 'required|date',
            'pickup_date' => 'required|date',
            'ar_no' => 'required|string|max:255',
            'grn_no' => 'required|string|max:255',
            'mega_pool_no' => 'required|string|max:255',
            'donor_id' => 'required|array',
            'donor_id.*' => 'required|string|max:255',
            'donation_date' => 'required|array',
            'donation_date.*' => 'required|date',
            'blood_group' => 'required|array',
            'blood_group.*' => 'required|string|in:A,B,AB,O',
            'bag_volume' => 'required|array',
            'bag_volume.*' => 'required|numeric|min:0',
            'mini_pool_bag_volume' => 'required|array',
            'mini_pool_bag_volume.*' => 'required|numeric|min:0',
            'segment_number' => 'required|array',
            'segment_number.*' => 'required|string|max:255',
            'tail_cutting' => 'required|array',
            'tail_cutting.*' => 'required|string|max:255',
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Prepare the data to send
        $postData = [
            'blood_centre' => $validatedData['blood_centre'],
            'work_station' => $validatedData['work_station'],
            'date' => $validatedData['date'],
            'pickup_date' => $validatedData['pickup_date'],
            'ar_no' => $validatedData['ar_no'],
            'grn_no' => $validatedData['grn_no'],
            'mega_pool_no' => $validatedData['mega_pool_no'],
            'bags' => [],
            'created_by' => Auth::id(),
        ];

        // Process each bag entry
        for ($i = 0; $i < count($validatedData['donor_id']); $i++) {
            $postData['bags'][] = [
                'donor_id' => $validatedData['donor_id'][$i],
                'donation_date' => $validatedData['donation_date'][$i],
                'blood_group' => $validatedData['blood_group'][$i],
                'bag_volume' => $validatedData['bag_volume'][$i],
                'mini_pool_bag_volume' => $validatedData['mini_pool_bag_volume'][$i],
                'segment_number' => $validatedData['segment_number'][$i],
                'tail_cutting' => $validatedData['tail_cutting'][$i],
            ];
        }

        // Define the external API URL
        $apiUrl = config('auth_api.bag_entry_store_url');

        try {
            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if ($apiResponse['success']) {
                    return redirect()->route('newBag.index')->with('success', 'Bag entries created successfully.');
                } else {
                    return back()->withErrors(['api_error' => $apiResponse['message']])->withInput();
                }
            } else {
                return back()->withErrors(['api_error' => 'Failed to create bag entries. Please try again.'])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Bag entry creation error: ' . $e->getMessage());
            return back()->withErrors(['api_error' => 'An error occurred while creating the bag entries.'])->withInput();
        }
    }
}