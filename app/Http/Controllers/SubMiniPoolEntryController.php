<?php

namespace App\Http\Controllers;

use App\Models\SubMiniPoolEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
} 