<?php
namespace App\View\Components;

use Illuminate\View\Component;

class SecondaryButton extends Component
{
    public $disabled;

    public function __construct($disabled = false)
    {
        $this->disabled = $disabled;
    }

    public function render()
    {
        return view('components.secondary-button');
    }
}