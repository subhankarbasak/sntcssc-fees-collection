<?php

// app/View/Components/Modal.php
namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $show;
    public $title;
    public $maxWidth;

    public function __construct($show, $title = '', $maxWidth = '2xl')
    {
        $this->show = $show;
        $this->title = $title;
        $this->maxWidth = $maxWidth;
    }

    public function render()
    {
        return view('components.modal');
    }
}