<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsistenciasExport implements FromCollection, WithHeadings
{
    protected $asistencias;

    public function __construct($asistencias)
    {
        $this->asistencias = $asistencias;
    }

    public function collection()
    {
        return $this->asistencias->map(function($a){

            return [
                'DNI'        => $a->empleado->dni,
                'Empleado'   => $a->empleado->apellidos . ' ' . $a->empleado->nombres,
                'Cargo'      => optional($a->empleado->cargoActual())->cargo ?? '---',
                'Fecha'      => $a->fecha,
                'Estado'     => ucfirst($a->estado_asistencia),

                'Entrada'    => $a->hora_entrada ?? '—',
                'Salida'     => $a->hora_salida ?? '—',
                'Break In'   => $a->break_inicio ?? '—',
                'Break Out'  => $a->break_fin ?? '—',

                'T. Inicio'  => $a->tardanza_inicio_turno . ' min',
                'T. Break'   => $a->tardanza_break . ' min',
                'Total'      => $a->tardanza_total . ' min',

                'Obs'        => $a->observaciones ?? '—',
            ];

        });
    }

    public function headings(): array
    {
        return [
            'DNI','Empleado','Cargo','Fecha','Estado',
            'Entrada','Salida','Break In','Break Out',
            'T. Inicio','T. Break','Total','Obs'
        ];
    }
}
