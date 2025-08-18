<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudRequest extends FormRequest
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
            'observacionesU' => 'required|max:255',
            'departamento_idU' => 'required|exists:departamentos,id',
            'maquina_idU' => 'required|exists:maquinas,id',
            'motivo_idU' => 'required|exists:motivos,id',
            'horasU' => 'required|numeric|min:1|max:16',
            'num_max_usuariosU' => 'required|integer|min:1',
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
            'observacionesU.required' => 'El campo Observaciones es requerido.',
            'observacionesU.max' => 'El campo Observaciones no debe ser mayor a 255 caracteres.',
            'departamento_idU.required' => 'El campo Departamento es requerido.',
            'departamento_idU.exists' => 'El campo Departamento no es válido.',
            'maquina_idU.required' => 'El campo Máquina es requerido.',
            'maquina_idU.exists' => 'El campo Máquina no es válido.',
            'motivo_idU.required' => 'El campo Motivo es requerido.',
            'motivo_idU.exists' => 'El campo Motivo no es válido.',
            'horasU.required' => 'El campo Horas es requerido.',
            'horasU.numeric' => 'El campo Horas debe ser un número.',
            'horasU.min' => 'El campo Horas no debe ser menor a 1.',
            'num_max_usuariosU.required' => 'El campo Número Máximo de empleados es requerido.',
            'num_max_usuariosU.integer' => 'El campo Número Máximo de empleados debe ser un número entero.',
            'num_max_usuariosU.min' => 'El campo Número Máximo de empleados no debe ser menor a 1.',
        ];
    }
}
