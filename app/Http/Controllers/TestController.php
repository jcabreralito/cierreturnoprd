<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Función para probar el store
     *
     * @return mixed
     */
    public function pruebaStore()
    {
        $a = DB::select("SET NOCOUNT ON; exec sp_GetEficienciaOperador ?,?,?", [
            2168,
            1,
            Carbon::parse('2025-08-21')->format('d/m/Y')
        ]);
        dd($a);
    }
}
