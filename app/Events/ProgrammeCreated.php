<?php

// app/Events/ProgrammeCreated.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Programme;

class ProgrammeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $programme;

    /**
     * Create a new event instance.
     *
     * @param Programme $programme
     * @return void
     */
    public function __construct(Programme $programme)
    {
        $this->programme = $programme;
    }
}