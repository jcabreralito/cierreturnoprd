<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'observaciones' => 'required|max:255',
            'departamento_id' => 'required|exists:departamentos,id',
            'maquina_id' => 'required|exists:maquinas,id',
            'motivo_id' => 'required|exists:motivos,id',
            'turno_id' => 'required|exists:turnos,id',
            'desde_dia' => 'required|date',
            'num_repeticiones' => 'required|integer|min:1|max:6',
            'horas' => 'required|numeric|min:1|max:16',
            'num_max_usuarios' => 'required|integer|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'observaciones.required' => 'El campo Observaciones es requerido.',
            'observaciones.max' => 'El campo Observaciones no debe ser mayor a 255 caracteres.',
            'departamento_id.required' => 'El campo Departamento es requerido.',
            'departamento_id.exists' => 'El campo Departamento no es válido.',
            'maquina_id.required' => 'El campo Máquina es requerido.',
            'maquina_id.exists' => 'El campo Máquina no es válido.',
            'motivo_id.required' => 'El campo Motivo es requerido.',
            'motivo_id.exists' => 'El campo Motivo no es válido.',
            'turno_id.required' => 'El campo Turno es requerido.',
            'turno_id.exists' => 'El campo Turno no es válido.',
            'desde_dia.required' => 'El campo Desde es requerido.',
            'desde_dia.date' => 'El campo Desde debe ser una fecha válida.',
            'num_repeticiones.required' => 'El campo Número de Repeticiones es requerido.',
            'num_repeticiones.integer' => 'El campo Número de Repeticiones debe ser un número entero.',
            'num_repeticiones.min' => 'El campo Número de Repeticiones no debe ser menor a 1.',
            'num_repeticiones.max' => 'El campo Número de Repeticiones no debe ser mayor a 6.',
            'horas.required' => 'El campo Horas es requerido.',
            'horas.numeric' => 'El campo Horas debe ser un número.',
            'horas.min' => 'El campo Horas no debe ser menor a 1.',
            'num_max_usuarios.required' => 'El campo Número Máximo de empleados es requerido.',
            'num_max_usuarios.integer' => 'El campo Número Máximo de empleados debe ser un número entero.',
            'num_max_usuarios.min' => 'El campo Número Máximo de empleados no debe ser menor a 1.',
        ];
    }
}
