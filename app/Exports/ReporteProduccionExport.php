<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteProduccionExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithColumnWidths
{
    protected $list;

    // Define los encabezados y los campos a exportar
    protected $headers = [
        [
            'N° ORDEN',
            'NOMBRE TRABAJO',
            'ID ACT',
            'DESCRIPCIÓN',
            'PROCESO',
            'CANTIDAD',
            'TURNO',
            'TIEMPO (HRS)',
            'FECHA INICIO',
            'FECHA FIN',
            'FECHA PRODUCCIÓN',
            'OPERADOR',
            'MAQUINA',
            'NOTAS'
        ],
    ];

    protected $fields = [
        'numOrden',
        'NombreTrabajo',
        'idAct',
        'observacion',
        'proceso',
        'Cantidad',
        'Turno',
        'Tiempo',
        'HoraInicio',
        'HoraFin',
        'fechaproduccion',
        'Empleado',
        'Maquina',
        'Notas'
    ];

    /**
     * Constructor para inicializar la lista de datos
     *
     * @param array $list
     * @return void
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    /**
     * Función para obtener la colección de datos a exportar
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->list);
    }

    /**
     * Función para obtener los encabezados del archivo Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->headers[0];
    }

    /**
     * Mapea cada fila para que coincida con los campos
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $mapped = [];
        foreach ($this->fields as $field) {
            $mapped[] = isset($row->$field) ? $row->$field : '';
        }
        return $mapped;
    }

    /**
     * Aplica estilos a la hoja de cálculo
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Aplica negrita y centrado a la fila de encabezados
        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    /**
     * Define los anchos de las columnas según el contenido esperado
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12,  // N° ORDEN
            'B' => 40,  // NOMBRE TRABAJO
            'C' => 17,  // ID ACT
            'D' => 34,  // DESCRIPCIÓN
            'E' => 30,  // PROCESO
            'F' => 10,  // CANTIDAD
            'G' => 7,  // TURNO
            'H' => 13,  // TIEMPO (HRS)
            'I' => 22,  // FECHA INICIO
            'J' => 22,  // FECHA FIN
            'K' => 22,  // FECHA PRODUCCIÓN
            'L' => 34,  // OPERADOR
            'M' => 14,  // MAQUINA
            'N' => 25,  // NOTAS
        ];
    }
}
