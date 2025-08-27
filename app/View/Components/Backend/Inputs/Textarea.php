<?php

namespace App\View\Components\Backend\Inputs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $name,
        public $rows = 3,
        public $placeholder = '',
        public $label = '',
        public $labelInline = true,
        public $rich = false,
        public $isRequired = true,
        public $isDisabled = false,
        public $value = null,
        public $aiGenerate = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.backend.inputs.textarea');
    }
}
