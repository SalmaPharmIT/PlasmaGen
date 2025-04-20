<?php

namespace App\Http\Controllers;

use App\Models\BagEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BagEntryController extends Controller
{
    /**
     * Show the bag entry form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('factory.newbagentry.bag_entry');
    }

    /**
     * Store a newly created bag entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Check for duplicate mega pool number
            $existingEntry = BagEntry::where('mega_pool_no', $request->mega_pool_no)->first();
            if ($existingEntry) {
                return redirect()->back()->with('error', 'Mega Pool Number already exists!')->withInput();
            }

            // Validate the request
            $validated = $request->validate([
                'blood_centre' => 'required|string',
                'work_station' => 'required|string',
                'date' => 'required|date',
                'pickup_date' => 'required|date',
                'ar_no' => 'required|string',
                'grn_no' => 'required|string',
                'mega_pool_no' => 'required|string|unique:bag_entries,mega_pool_no',
                'donor_id' => 'array',
                'donation_date' => 'array',
                'blood_group' => 'array',
                'bag_volume' => 'array',
                'mini_pool_bag_volume' => 'array',
                'segment_number' => 'array',
                'tail_cutting' => 'array',
            ]);

            // Prepare bag details
            $bagDetails = [];
            $totalVolume = 0;

            // Process each bag
            for ($i = 0; $i < count($request->donor_id); $i++) {
                // Skip empty rows
                if (empty($request->donor_id[$i]) && empty($request->bag_volume[$i])) {
                    continue;
                }

                $bagDetails[] = [
                    'donor_id' => $request->donor_id[$i] ?? null,
                    'donation_date' => $request->donation_date[$i] ?? null,
                    'blood_group' => $request->blood_group[$i] ?? null,
                    'bag_volume' => $request->bag_volume[$i] ?? null,
                    'mini_pool_bag_volume' => $request->mini_pool_bag_volume[$i] ?? null,
                    'segment_number' => $request->segment_number[$i] ?? null,
                    'tail_cutting' => $request->tail_cutting[$i] ?? null,
                ];

                // Add to total volume if it's a mini pool volume entry
                if (!empty($request->mini_pool_bag_volume[$i])) {
                    $totalVolume += floatval($request->mini_pool_bag_volume[$i]);
                }
            }

            // Create the bag entry
            $bagEntry = BagEntry::create([
                'blood_centre' => $validated['blood_centre'],
                'work_station' => $validated['work_station'],
                'date' => $validated['date'],
                'pickup_date' => $validated['pickup_date'],
                'ar_no' => $validated['ar_no'],
                'grn_no' => $validated['grn_no'],
                'mega_pool_no' => $validated['mega_pool_no'],
                'bag_details' => $bagDetails,
                'total_volume' => $totalVolume,
            ]);

            DB::commit();

            return redirect()->route('newBag.index')->with('success', 'Bag entry saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving bag entry: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error saving bag entry: ' . $e->getMessage())->withInput();
        }
    }

    public function show(BagEntry $bagEntry)
    {
        return view('factory.newbagentry.show', compact('bagEntry'));
    }

    /**
     * Check if a mega pool number already exists.
     *
     * @param  string  $megaPoolNo
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMegaPool($megaPoolNo)
    {
        $exists = BagEntry::where('mega_pool_no', $megaPoolNo)->exists();
        return response()->json(['exists' => $exists]);
    }
}