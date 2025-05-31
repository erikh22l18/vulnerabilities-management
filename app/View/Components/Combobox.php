<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Combobox extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $placeholder;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        $name = '',
        $label = '',
        $options = [],
        $selected = [],
        $placeholder = ''
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.combobox');
    }
}
