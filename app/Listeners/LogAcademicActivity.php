<?php

// app/Listeners/LogAcademicActivity.php

namespace App\Listeners;

use App\Events\ProgrammeCreated;
use App\Events\BatchCreated;
use App\Events\SectionCreated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogAcademicActivity implements ShouldQueue
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
        $model = null;
        $modelId = null;
        
        if ($event instanceof ProgrammeCreated) {
            $action = 'create';
            $description = "Created programme: {$event->programme->name}";
            $model = $event->programme;
            $modelId = $event->programme->id;
        } elseif ($event instanceof BatchCreated) {
            $action = 'create';
            $description = "Created batch: {$event->batch->name} for programme: {$event->batch->programme->name}";
            $model = $event->batch;
            $modelId = $event->batch->id;
        } elseif ($event instanceof SectionCreated) {
            $action = 'create';
            $description = "Created section: {$event->section->name} for batch: {$event->section->batch->name}";
            $model = $event->section;
            $modelId = $event->section->id;
        }
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}