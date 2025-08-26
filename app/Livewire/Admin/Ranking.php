<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Ranking extends Component
{
    /**
     * Función para renderizar la vista del ranking
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.ranking')->layout('layouts.main');
    }
}
