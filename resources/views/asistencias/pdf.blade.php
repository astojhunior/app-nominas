<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencias</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: center; }

        th { background: #0A4BD6; color: white; }

        .estado-turno { background: #22c55e; color: white; font-weight: bold; }
        .estado-descanso { background: #9ca3af; color: white; font-weight: bold; }
        .estado-falta { background: #ef4444; color: white; font-weight: bold; }
        .estado-licencia { background: #38bdf8; color: white; font-weight: bold; }
        .estado-permiso { background: #f97316; color: white; font-weight: bold; }

        h2 { color: #0A4BD6; margin-bottom: 5px; }
    </style>
</head>

<body>

    <h2>Reporte de Asistencias</h2>

    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Empleado</th>
                <th>Cargo</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Break</th>
                <th>T. Inicio</th>
                <th>T. Break</th>
                <th>Total</th>
                <th>Observaciones</th>
            </tr>
        </thead>

        <tbody>

        @forelse($asistencias as $a)

            @php
                $estado = strtolower($a->estado_asistencia);
                $claseEstado =
                    $estado === 'turno' ? 'estado-turno' :
                    ($estado === 'descanso' ? 'estado-descanso' :
                    ($estado === 'falta' ? 'estado-falta' :
                    ($estado === 'licencia' ? 'estado-licencia' :
                    'estado-permiso')));
            @endphp

           <tr>

    <td>{{ $a->empleado->dni }}</td>

    <td>{{ $a->empleado->apellidos }} {{ $a->empleado->nombres }}</td>

    <td>{{ optional($a->empleado->cargoActual())->cargo ?? '---' }}</td>

    <td>{{ $a->fecha }}</td>

    <!-- Estado con colores -->
    <td style="
        color:white;
        font-weight:bold;
        background:
            {{ $a->estado_asistencia == 'turno' ? '#22c55e' :
               ($a->estado_asistencia == 'descanso' ? '#9ca3af' :
               ($a->estado_asistencia == 'falta' ? '#ef4444' :
               ($a->estado_asistencia == 'licencia' ? '#38bdf8' : '#f97316'))) }};
        text-align:center;
    ">
        {{ ucfirst($a->estado_asistencia) }}
    </td>

    @if(in_array($a->estado_asistencia, ['descanso', 'falta']))
        <td>---</td>
        <td>---</td>
        <td>---</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td><i>{{ ucfirst($a->estado_asistencia) }} — No requiere registro de jornada</i></td>
    @else
        <td>{{ $a->hora_entrada ?? '---' }}</td>
        <td>{{ $a->hora_salida ?? '---' }}</td>
        <td>{{ $a->break_inicio ?? '---' }} - {{ $a->break_fin ?? '---' }}</td>
        <td>{{ $a->tardanza_inicio_turno ?? 0 }}</td>
        <td>{{ $a->tardanza_break ?? 0 }}</td>
        <td>{{ $a->tardanza_total ?? 0 }}</td>
        <td>{{ $a->observaciones ?? '—' }}</td>
    @endif

</tr>


        @empty
            <tr>
                <td colspan="12">No existen registros con estos filtros.</td>
            </tr>
        @endforelse

        </tbody>
    </table>

</body>
</html>
