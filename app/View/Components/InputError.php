<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\MessageBag;

class InputError extends Component
{
    public $messages;

    public function __construct($messages = [])
    {
        $this->messages = $messages instanceof MessageBag ? $messages->all() : (array) $messages;
    }

    public function render()
    {
        return view('components.input-error');
    }
}