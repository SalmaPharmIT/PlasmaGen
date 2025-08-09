<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditTrailController extends Controller
{
    /**
     * Display the audit trail report view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get distinct modules for filter dropdown
        $modules = AuditTrail::select('module')
            ->distinct()
            ->whereNotNull('module')
            ->orderBy('module')
            ->pluck('module');
            
        // Get distinct actions for filter dropdown
        $actions = AuditTrail::select('action')
            ->distinct()
            ->whereNotNull('action')
            ->orderBy('action')
            ->pluck('action');

        // Get distinct users for filter dropdown
        $users = AuditTrail::select('user_id', 'user_name')
            ->distinct()
            ->whereNotNull('user_id')
            ->whereNotNull('user_name')
            ->orderBy('user_name')
            ->get();

        return view('audit.index', compact('modules', 'actions', 'users'));
    }

    /**
     * Get filtered audit trail data for DataTables AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuditTrailData(Request $request)
    {
        $query = AuditTrail::query();

        // Apply filters
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Handle DataTables search functionality
        if ($request->has('search') && $request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhere('record_id', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Handle DataTables sorting
        if ($request->has('order') && $request->order) {
            $columns = [
                'id', 'user_name', 'user_role', 'module', 
                'action', 'record_id', 'description', 'created_at'
            ];
            
            $columnIdx = $request->input('order.0.column');
            $direction = $request->input('order.0.dir');
            
            if (isset($columns[$columnIdx])) {
                $query->orderBy($columns[$columnIdx], $direction);
            }
        } else {
            // Order by created_at by default, newest first
            $query->orderBy('created_at', 'desc');
        }

        // Get total count for pagination
        $recordsTotal = AuditTrail::count();
        $recordsFiltered = $query->count();

        // Paginate the results
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $auditTrails = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $auditTrails,
        ]);
    }

    /**
     * Show the details of a specific audit trail record
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $auditTrail = AuditTrail::findOrFail($id);
        
        // Parse JSON data
        $oldValues = $auditTrail->old_values ? json_decode($auditTrail->old_values, true) : [];
        $newValues = $auditTrail->new_values ? json_decode($auditTrail->new_values, true) : [];
        
        return view('audit.show', compact('auditTrail', 'oldValues', 'newValues'));
    }

    /**
     * Export audit trail data to CSV
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $query = AuditTrail::query();
        
        // Apply the same filters as in getAuditTrailData
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Order by created_at
        $query->orderBy('created_at', 'desc');
        
        // Get all matching records
        $auditTrails = $query->get();
        
        // Generate CSV
        $filename = 'audit_trail_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($auditTrails) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 
                'User', 
                'Role', 
                'Action', 
                'Module', 
                'Section', 
                'Record ID', 
                'Description', 
                'IP Address', 
                'Date/Time'
            ]);
            
            // Add data rows
            foreach ($auditTrails as $trail) {
                fputcsv($file, [
                    $trail->id,
                    $trail->user_name,
                    $trail->user_role,
                    $trail->action,
                    $trail->module,
                    $trail->section,
                    $trail->record_id,
                    $trail->description,
                    $trail->ip_address,
                    $trail->created_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->streamDownload($callback, $filename, $headers);
    }
}
