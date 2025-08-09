<?php

namespace App\Http\Controllers;

use App\Models\SubMiniPoolEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubMiniPoolEntryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mega_pool_no' => 'nullable|string',
                'mini_pool_number' => 'nullable|string',
                'sub_mini_pool_no' => 'nullable|string',
                'timestamp' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Add user tracking
            $data['created_by'] = Auth::user()->name ?? null;
            $data['updated_by'] = Auth::user()->name ?? null;

            // If sub_mini_pool_no is an array, convert it to comma-separated string
            if (is_array($data['sub_mini_pool_no'])) {
                $data['sub_mini_pool_no'] = implode(',', $data['sub_mini_pool_no']);
            }

            // Log the data being saved
            Log::info('Saving SubMiniPoolEntry', $data);

            $subMiniPoolEntry = SubMiniPoolEntry::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Sub Mini Pool Entry created successfully',
                'data' => $subMiniPoolEntry
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating SubMiniPoolEntry: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Sub Mini Pool Entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMiniPoolNumbers(Request $request)
    {
        try {
            $miniPoolNumbers = SubMiniPoolEntry::select('mini_pool_number')
                ->whereNotNull('mini_pool_number')
                ->where('mini_pool_number', '!=', '')
                ->distinct()
                ->orderBy('mini_pool_number')
                ->pluck('mini_pool_number')
                ->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $miniPoolNumbers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mini pool numbers: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch mini pool numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubMiniPoolNumbers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mini_pool_number' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mini Pool Number is required'
                ], 400);
            }

            // Get sub mini pool numbers
            $subMiniPoolEntry = SubMiniPoolEntry::where('mini_pool_number', $request->mini_pool_number)
                ->first();

            if (!$subMiniPoolEntry) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No sub mini pool numbers found for this mini pool'
                ], 404);
            }

            // Get bag entries detail IDs from bag_entries_mini_pools
            $bagEntriesMiniPool = DB::table('bag_entries_mini_pools')
                ->where('mini_pool_number', $request->mini_pool_number)
                ->first();

            if (!$bagEntriesMiniPool) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No bag entries found for this mini pool'
                ], 404);
            }

            // Split the comma-separated sub mini pool numbers into an array
            $subMiniPoolNumbers = explode(',', $subMiniPoolEntry->sub_mini_pool_no);

            // Get the count of bag entries detail IDs
            $bagEntriesDetailIds = explode(',', trim($bagEntriesMiniPool->bag_entries_detail_ids, '[]'));
            $rowCount = count($bagEntriesDetailIds);

            return response()->json([
                'status' => 'success',
                'data' => $subMiniPoolNumbers,
                'row_count' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sub mini pool numbers: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sub mini pool numbers: ' . $e->getMessage()
            ], 500);
        }
    }
}