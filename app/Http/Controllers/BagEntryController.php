<?php

namespace App\Http\Controllers;

use App\Models\BagEntry;
use App\Models\Entity;
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
        $bloodCenters = Entity::where('entity_type_id', 2)
            ->where('account_status', Entity::STATUS_ACTIVE)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return view('factory.newbagentry.bag_entry', compact('bloodCenters'));
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
                'blood_centre_id' => 'required|exists:entities,id',
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

            // Create the main bag entry
            $bagEntry = BagEntry::create([
                'blood_bank_id' => $validated['blood_centre_id'], // Map blood_centre_id to blood_bank_id
                'work_station' => $validated['work_station'],
                'date' => $validated['date'],
                'pickup_date' => $validated['pickup_date'],
                'ar_no' => $validated['ar_no'],
                'grn_no' => $validated['grn_no'],
                'mega_pool_no' => $validated['mega_pool_no'],
                'total_mini_pool_volume' => 0, // Will be updated later
            ]);

            $totalVolume = 0;
            $currentMiniPoolDetails = [];
            $miniPoolCount = 0;

            // Process each bag entry (72 entries in total)
            for ($i = 0; $i < count($request->donor_id); $i++) {
                // Calculate which mini pool group this belongs to (1-6 for 72 entries)
                $miniPoolGroup = floor($i / 12) + 1;
                
                // Create bag entry detail
                $bagDetail = DB::table('bag_entries_details')->insertGetId([
                    'bag_entries_id' => $bagEntry->id,
                    'no_of_bags' => $i + 1,
                    'bags_in_mini_pool' => (($i % 12) + 1),
                    'donor_id' => $request->donor_id[$i],
                    'donation_date' => $request->donation_date[$i],
                    'blood_group' => $request->blood_group[$i],
                    'bag_volume_ml' => $request->bag_volume[$i],
                    'tail_cutting' => $request->tail_cutting[$i],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $currentMiniPoolDetails[] = $bagDetail;

                // Every 12 entries, create a mini pool entry
                if (($i + 1) % 12 === 0) {
                    $miniPoolIndex = floor($i / 12);
                    if (isset($request->mini_pool_bag_volume[$miniPoolIndex]) && 
                        isset($request->segment_number[$miniPoolIndex])) {
                        
                        $miniPoolVolume = floatval($request->mini_pool_bag_volume[$miniPoolIndex]);
                        $totalVolume += $miniPoolVolume;

                        // Create mini pool entry
                        DB::table('bag_entries_mini_pools')->insert([
                            'bag_entries_id' => $bagEntry->id,
                            'bag_entries_detail_ids' => json_encode($currentMiniPoolDetails),
                            'mini_pool_bag_volume' => $miniPoolVolume,
                            'mini_pool_number' => $request->segment_number[$miniPoolIndex],
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Reset current mini pool details array
                        $currentMiniPoolDetails = [];
                        $miniPoolCount++;
                    }
                }
            }

            // Update the total volume in the main entry
            $bagEntry->update([
                'total_mini_pool_volume' => $totalVolume
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

    /**
     * Show the sub mini pool bag entry form.
     *
     * @return \Illuminate\View\View
     */
    public function subMiniPoolBagEntry()
    {
        return view('factory.newbagentry.sub_mini_pool_bag_entry');
    }
}