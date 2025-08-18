<?php

namespace App\Observers;

use App\Models\Solicitud;
use Illuminate\Support\Str;

class SolicitudObserver
{
    /**
     * Handle the Solicitud "creating" event.
     */
    public function creating(Solicitud $solicitud)
    {
        $solicitud->slug = Str::random(15);
    }
}
