<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $placeholder;
    public $labelText;
    public $isRequired;
    public $showErrors;
    public $value;
    public $isDisabled;
    public $typeL;
    public $isReadOnly;
    public $hasWireModel;
    public $hasEtiqueta;

    /**
     * Create a new component instance.
     */
    public function __construct($name = '', $placeholder = 'Ingresa un valor', $labelText  = 'Ingresa un valor', $isRequired = true, $showErrors = true, $value = null, $isDisabled = false, $typeL = 2, $isReadonly = false, $hasWireModel = true, $hasEtiqueta = true)
    {
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->labelText = $labelText;
        $this->isRequired = $isRequired;
        $this->showErrors = $showErrors;
        $this->value = $value;
        $this->isDisabled = $isDisabled;
        $this->typeL = $typeL;
        $this->isReadOnly = $isReadonly;
        $this->hasWireModel = $hasWireModel;
        $this->hasEtiqueta = $hasEtiqueta;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.forms.select');
    }
}
