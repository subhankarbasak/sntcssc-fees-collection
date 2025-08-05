<?php
// app/Listeners/LogFeeActivity.php

namespace App\Listeners;

use App\Events\FeeCreated;
use App\Events\FeeUpdated;
use App\Events\FeeDeleted;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogFeeActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $action = '';
        $description = '';
        
        if ($event instanceof FeeCreated) {
            $action = 'create';
            $description = "Created fee type: {$event->feeType->name}";
        } elseif ($event instanceof FeeUpdated) {
            $action = 'update';
            $description = "Updated fee type: {$event->feeType->name}";
        } elseif ($event instanceof FeeDeleted) {
            $action = 'delete';
            $description = "Deleted fee type: {$event->feeType->name}";
        }
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($event->feeType),
            'model_id' => $event->feeType->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}