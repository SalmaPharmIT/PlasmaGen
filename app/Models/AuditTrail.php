<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'user_name',
        'user_role',
        'action',
        'module',
        'section',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description'
    ];

    /**
     * Log an activity in the audit trail
     *
     * @param string $action The action performed (create, update, delete, etc.)
     * @param string $module The module where the action was performed
     * @param string $section Optional section within the module
     * @param mixed $recordId ID of the affected record
     * @param array $oldValues Previous values (for updates)
     * @param array $newValues New values (for creates and updates)
     * @param string $description Human-readable description of the action
     * @return AuditTrail
     */
    public static function log(
        string $action, 
        string $module, 
        string $section = null, 
        $recordId = null, 
        array $oldValues = [], 
        array $newValues = [], 
        string $description = null
    ) {
        $user = Auth::user();
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'user_role' => $user ? getRoleName($user->role_id) : null,
            'action' => $action,
            'module' => $module,
            'section' => $section,
            'record_id' => $recordId,
            'old_values' => !empty($oldValues) ? json_encode($oldValues) : null,
            'new_values' => !empty($newValues) ? json_encode($newValues) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'description' => $description
        ]);
    }

    /**
     * Get the user that performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

/**
 * Helper function to get role name from role_id
 * 
 * @param int $roleId
 * @return string|null
 */
function getRoleName($roleId)
{
    $roles = [
        1 => 'Super Admin',
        2 => 'Company Admin',
        6 => 'RBE',
        7 => 'Logistics Admin',
        8 => 'Sourcing Agent',
        9 => 'Collecting Agent',
        12 => 'Factory Admin',
        15 => 'Lab Technician',
        16 => 'Quality Control',
        17 => 'Production Technician',
        18 => 'Manager',
        19 => 'Supervisor'
    ];

    return $roles[$roleId] ?? 'Unknown Role';
}
