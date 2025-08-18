<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class v_HorasExtras extends Model
{
    use HasFactory;

    protected $table = 'v_HorasExtras';
    protected $primaryKey = 'idSolicitud';
    public $timestamps = false;

    /**
     * Función para filtrar las solicitudes.
     *
     * @param $query
     * @param $data
     * @param $departamentos
     * @return mixed
     */
    public function scopeFilters($query, $data, $departamentos)
    {
        return $query->when($data['filtroFolio'] && is_numeric($data['filtroFolio']), function ($query) use ($data) {
                    return $query->where('folio', trim($data['filtroFolio']));
                })
                ->when($data['filtroDepartamento'], function ($query) use ($data) {
                    return $query->where('departamento_id', $data['filtroDepartamento']);
                })
                ->when($data['filtroMaquina'], function ($query) use ($data) {
                    return $query->where('maquina_id', $data['filtroMaquina']);
                })
                ->when($data['filtroEstatus'], function ($query) use ($data) {
                    return $query->where('estatus_id', $data['filtroEstatus']);
                })
                ->when($data['filtroMotivo'], function ($query) use ($data) {
                    return $query->where('motivo_id', $data['filtroMotivo']);
                })
                ->when($data['filtroTurno'], function ($query) use ($data) {
                    return $query->where('turno_id', $data['filtroTurno']);
                })
                ->when($data['filtroFecha'], function ($query) use ($data) {
                    return $query->where('desde_dia', $data['filtroFecha']);
                })
                ->when($data['filtroPersonal'], function ($query) use ($data) {
                    // buscamos la op en la tabla de ops_solicitudes
                    return $query->whereIn('idSolicitud', function ($query) use ($data) {
                        $query->select('solicitud_id')
                            ->from('solicitud_usuarios')
                            ->where('personal', $data['filtroPersonal']);
                    });
                })
                ->when($data['filtroOp'], function ($query) use ($data) {
                    // buscamos la op en la tabla de ops_solicitudes
                    return $query->whereIn('idSolicitud', function ($query) use ($data) {
                        $query->select('solicitud_id')
                            ->from('ops_solicitudes')
                            ->where('op', $data['filtroOp']);
                    });
                })
                ->when($data['filtroObservaciones'], function ($query) use ($data) {
                    return $query->where('observaciones', 'like', '%' . $data['filtroObservaciones'] . '%');
                })
                ->when($data['filtroSemana'], function ($query) use ($data) {
                    return $query->whereIn('desde_dia', function ($query) use ($data) {
                        $query->select('FECHA')
                            ->from('ETL_MSTR.dbo.etl_CatSemanas')
                            ->where('SEMCOMPLETA', $data['filtroSemana']);
                    });
                })
                ->when($data['filtroPersonalNombre'], function ($query) use ($data) {
                    return $query->whereIn('idSolicitud', function ($queryA) use ($data) {
                        $queryA->select('solicitud_id')
                            ->from('solicitud_usuarios')
                            ->whereIn('personal', function ($queryB) use ($data) {
                                $queryB->select('Personal')
                                    ->from('v_Personal')
                                    ->where('nombre', 'like', '%' . $data['filtroPersonalNombre'] . '%');
                            });
                    });
                })
                ->when(auth()->user()->tipoUsuarioHorasExtra == 3 && !in_array(auth()->user()->Id_Usuario, [12436, 12460]), function ($query) use ($departamentos) {
                    return $query->whereIn('departamento_id', $departamentos);
                });
    }
}
