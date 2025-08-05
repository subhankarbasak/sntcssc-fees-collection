<?php

// app/Events/SectionCreated.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Section;

class SectionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $section;

    /**
     * Create a new event instance.
     *
     * @param Section $section
     * @return void
     */
    public function __construct(Section $section)
    {
        $this->section = $section;
    }
}