<?php
// app/Events/StudentCreated.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class StudentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $student;

    /**
     * Create a new event instance.
     *
     * @param Student $student
     * @return void
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
    }
}