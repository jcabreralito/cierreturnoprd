<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Historico extends Component
{
    /**
     * Función para renderizar la vista del histórico
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.historico')->layout('layouts.main');
    }
}
