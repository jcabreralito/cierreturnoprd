<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListRegistroExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithColumnWidths
{
    protected $list;

    // Define los encabezados y los campos a exportar
    protected $headers = [
        ['N° ORDEN', 'NOMBRE TRABAJO', 'DESCRIPCIÓN', 'PROCESO', 'CANTIDAD', 'TIEMPO', 'HORA INICIO', 'HORA FIN', 'MAQUINA', 'NOTAS'],
    ];

    protected $fields = [
        'numOrden', 'NombreTrabajo', 'observacion', 'proceso', 'Cantidad', 'Tiempo', 'HoraInicio', 'HoraFin', 'Maquina', 'Notas'
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
            'C' => 30,  // DESCRIPCIÓN
            'D' => 18,  // PROCESO
            'E' => 10,  // CANTIDAD
            'F' => 10,  // TIEMPO
            'G' => 15,  // HORA INICIO
            'H' => 15,  // HORA FIN
            'I' => 20,  // MAQUINA
            'J' => 25,  // NOTAS
        ];
    }
}
