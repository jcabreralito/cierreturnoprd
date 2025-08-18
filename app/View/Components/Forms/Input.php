<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public $name;
    public $type;
    public $placeholder;
    public $labelText;
    public $isRequired;
    public $showErrors;
    public $value;
    public $isDisabled;
    public $isReadOnly;

    /**
     * Create a new component instance.
     */
    public function __construct($name = '', $type  = 'text', $placeholder = 'Ingresa un valor', $labelText  = 'Ingresa un valor', $isRequired = true, $showErrors = true, $value = null, $isDisabled = false, $isReadOnly = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->placeholder = $placeholder;
        $this->labelText = $labelText;
        $this->isRequired = $isRequired;
        $this->showErrors = $showErrors;
        $this->value = $value;
        $this->isDisabled = $isDisabled;
        $this->isReadOnly = $isReadOnly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.input');
    }
}
