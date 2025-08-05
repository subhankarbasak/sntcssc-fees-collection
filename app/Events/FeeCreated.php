<?php
// app/Events/FeeCreated.php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FeeType;

class FeeCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feeType;

    /**
     * Create a new event instance.
     *
     * @param FeeType $feeType
     * @return void
     */
    public function __construct(FeeType $feeType)
    {
        $this->feeType = $feeType;
    }
}