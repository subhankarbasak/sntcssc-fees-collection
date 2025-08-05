<?php

// app/Events/BatchCreated.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Batch;

class BatchCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $batch;

    /**
     * Create a new event instance.
     *
     * @param Batch $batch
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }
}