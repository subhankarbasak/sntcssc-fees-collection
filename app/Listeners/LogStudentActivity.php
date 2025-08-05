<?php

// app/Listeners/LogStudentActivity.php

namespace App\Listeners;

use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use App\Events\StudentDeleted;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class LogStudentActivity implements ShouldQueue
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
        
        if ($event instanceof StudentCreated) {
            $action = 'create';
            $description = "Created student: {$event->student->student_id} - {$event->student->user->name}";
        } elseif ($event instanceof StudentUpdated) {
            $action = 'update';
            $description = "Updated student: {$event->student->student_id} - {$event->student->user->name}";
        } elseif ($event instanceof StudentDeleted) {
            $action = 'delete';
            $description = "Deleted student: {$event->student->student_id} - {$event->student->user->name}";
        }
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($event->student),
            'model_id' => $event->student->id,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}