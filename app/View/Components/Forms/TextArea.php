<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextArea extends Component
{
    public $name;
    public $placeholder;
    public $labelText;
    public $isRequired;
    public $showErrors;
    public $value;

    /**
     * Create a new component instance.
     */
    public function __construct($name = '', $placeholder = 'Ingresa un valor', $labelText  = 'Ingresa un valor', $isRequired = true, $showErrors = true, $value = null)
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->labelText = $labelText;
        $this->isRequired = $isRequired;
        $this->showErrors = $showErrors;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.text-area');
    }
}
