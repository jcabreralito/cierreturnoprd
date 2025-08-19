<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BitacoraController extends Controller
{
    /**
     * Función para registrar una nueva entrada en la bitácora.
     *
     * @param $data
     * @return mixed
     */
    public function registrarBitacora($data)
    {
        try {
            DB::beginTransaction();
            $bitacora = Bitacora::create($data);
            DB::commit();
            return $bitacora;
        } catch (\Exception $e) {
            DB::rollBack();
            return "Lo sentimos ocurrió un error";
        }
    }
}
