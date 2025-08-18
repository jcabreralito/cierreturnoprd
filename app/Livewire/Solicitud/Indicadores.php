<?php

namespace App\Livewire\Solicitud;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Indicadores extends Component
{
    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.solicitud.indicadores')->layout('layouts.main');
    }
}
