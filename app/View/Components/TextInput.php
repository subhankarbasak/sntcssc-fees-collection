<?php
namespace App\View\Components;

use Illuminate\View\Component;

class TextInput extends Component
{
    public $name;
    public $id;
    public $type;
    public $value;
    public $required;

    public function __construct($name = null, $id = null, $type = 'text', $value = null, $required = false)
    {
        $this->name = $name;
        $this->id = $id;
        $this->type = $type;
        $this->value = $value;
        $this->required = $required;
    }

    public function render()
    {
        return view('components.text-input');
    }
}