<?php

// app/Observers/AuditLogObserver.php
namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogObserver
{
    public function created($model)
    {
        $this->logAction($model, 'created');
    }
    
    public function updated($model)
    {
        $this->logAction($model, 'updated');
    }
    
    public function deleted($model)
    {
        $this->logAction($model, 'deleted');
    }
    
    public function restored($model)
    {
        $this->logAction($model, 'restored');
    }
    
    public function forceDeleted($model)
    {
        $this->logAction($model, 'force_deleted');
    }
    
    protected function logAction($model, $action)
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        $oldValues = null;
        $newValues = null;
        
        if ($action === 'updated') {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();
        }
        
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $this->getDescription($model, $action),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    protected function getDescription($model, $action)
    {
        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->title ?? $model->id ?? 'Unknown';
        
        return ucfirst($action) . " {$modelName}: {$identifier}";
    }
}