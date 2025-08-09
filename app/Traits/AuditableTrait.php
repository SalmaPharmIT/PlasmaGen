<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait AuditableTrait
{
    /**
     * Boot the trait
     *
     * @return void
     */
    public static function bootAuditableTrait()
    {
        // Log model events
        static::created(function (Model $model) {
            $model->logAudit('create');
        });

        static::updated(function (Model $model) {
            $model->logAudit('update');
        });

        static::deleted(function (Model $model) {
            $model->logAudit('delete');
        });
    }

    /**
     * Log the audit event
     *
     * @param string $action
     * @return void
     */
    public function logAudit(string $action)
    {
        // Get the module name from the model class
        $modelClass = class_basename($this);
        $module = $this->getAuditModule() ?? $modelClass;
        $section = $this->getAuditSection() ?? null;
        
        // Get changes for updates
        $oldValues = [];
        $newValues = [];
        
        if ($action === 'update') {
            $changedAttributes = $this->getDirty();
            foreach ($changedAttributes as $key => $value) {
                // Skip timestamp fields
                if (in_array($key, ['created_at', 'updated_at'])) {
                    continue;
                }
                
                $oldValues[$key] = $this->getOriginal($key);
                $newValues[$key] = $value;
            }
            
            // If no meaningful changes, don't log
            if (empty($oldValues) && empty($newValues)) {
                return;
            }
        } elseif ($action === 'create') {
            // For create, log all attributes except timestamps
            $attributes = $this->getAttributes();
            foreach ($attributes as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at'])) {
                    $newValues[$key] = $value;
                }
            }
        } elseif ($action === 'delete') {
            // For delete, log all attributes as old values
            $attributes = $this->getAttributes();
            foreach ($attributes as $key => $value) {
                if (!in_array($key, ['created_at', 'updated_at'])) {
                    $oldValues[$key] = $value;
                }
            }
        }
        
        // Generate a human-readable description
        $description = $this->getAuditDescription($action, $oldValues, $newValues);
        
        // Log the audit trail
        AuditTrail::log(
            $action,
            $module,
            $section,
            $this->id ?? null,
            $oldValues,
            $newValues,
            $description
        );
    }
    
    /**
     * Override this method in your model to customize the module name
     *
     * @return string|null
     */
    public function getAuditModule()
    {
        return null;
    }
    
    /**
     * Override this method in your model to customize the section name
     *
     * @return string|null
     */
    public function getAuditSection()
    {
        return null;
    }
    
    /**
     * Generate a human-readable description of the change
     *
     * @param string $action
     * @param array $oldValues
     * @param array $newValues
     * @return string
     */
    protected function getAuditDescription(string $action, array $oldValues, array $newValues): string
    {
        $modelName = class_basename($this);
        $modelId = $this->id ?? 'new';
        
        switch ($action) {
            case 'create':
                return "Created new {$modelName} record with ID: {$modelId}";
                
            case 'update':
                $changedFields = array_keys($newValues);
                $fieldList = implode(', ', $changedFields);
                return "Updated {$modelName} record ID: {$modelId}. Changed fields: {$fieldList}";
                
            case 'delete':
                return "Deleted {$modelName} record with ID: {$modelId}";
                
            default:
                return "Performed {$action} on {$modelName} record with ID: {$modelId}";
        }
    }
} 