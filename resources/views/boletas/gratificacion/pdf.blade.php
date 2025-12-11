<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .titulo { font-size: 18px; font-weight: bold; color: #0A3AFF; margin-bottom: 14px; }
        .seccion-title { font-weight: bold; margin: 18px 0 6px; color:#0A3AFF; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #0A3AFF; color: white; padding: 6px; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 5px; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .firma { margin-top: 40px; text-align: center; font-size: 12px; }
    </style>
</head>

<body>

    <div class="titulo">Boleta de Pago - Gratificación {{ $mesNombre }} {{ $anio }}</div>

    <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
    <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
    <p><strong>Cargo:</strong> {{ $empleado->contratoActual->cargo->cargo ?? 'Sin cargo' }}</p>
    <p><strong>Fecha de generación:</strong> {{ $boleta->fecha_generacion?->format('d/m/Y') }}</p>

    <div class="seccion-title">Remuneración computable</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>
        <tr>
            <td>Sueldo básico</td>
            <td class="right">{{ number_format($calculo['sueldo'], 2) }}</td>
        </tr>
        <tr>
            <td>Asignación familiar</td>
            <td class="right">{{ number_format($calculo['asignacion_familiar'], 2) }}</td>
        </tr>
        <tr>
            <td>Bonificación fija</td>
            <td class="right">{{ number_format($calculo['bonificacion_fija'], 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Remuneración computable</td>
            <td class="right bold">{{ number_format($calculo['base_remuneracion'], 2) }}</td>
        </tr>
    </table>

    <div class="seccion-title">Detalle del período</div>
    <table>
        <tr><th>Descripción</th><th class="right">Detalle</th></tr>
        <tr>
            <td>Semestre legal</td>
            <td class="right">
                @if($boleta->periodo_mes == 7)
                    01/01/{{ $anio }} al 30/06/{{ $anio }}
                @else
                    01/07/{{ $anio }} al 31/12/{{ $anio }}
                @endif
            </td>
        </tr>
        <tr>
            <td>Tiempo efectivo</td>
            <td class="right">
                {{ $calculo['inicio_efectivo']->format('d/m/Y') }}
                al
                {{ $calculo['fin_efectivo']->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td>Días trabajados</td>
            <td class="right">{{ $calculo['dias_trabajados'] }}</td>
        </tr>
        <tr>
            <td>Meses equivalentes</td>
            <td class="right">
                {{ $calculo['meses_equivalentes'] }} mes(es)
                + {{ $calculo['dias_residuo'] }} día(s)
            </td>
        </tr>
    </table>

    <div class="seccion-title">Ingresos</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>
        <tr>
            <td>Gratificación legal {{ $mesNombre }}</td>
            <td class="right">{{ number_format($calculo['gratificacion'], 2) }}</td>
        </tr>
        <tr>
            <td>Bonificación extraordinaria Essalud 9%</td>
            <td class="right">{{ number_format($calculo['bonificacion_essalud'], 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Total ingresos</td>
            <td class="right bold">{{ number_format($calculo['total_ingresos'], 2) }}</td>
        </tr>
    </table>

    <div class="seccion-title">Resumen final</div>
    <table>
        <tr>
            <td class="bold">Neto a pagar</td>
            <td class="right bold">{{ number_format($calculo['neto_pagar'], 2) }}</td>
        </tr>
    </table>

    <div class="firma">
        _______________________________<br>
        FIRMA CONFORME
    </div>

</body>
</html>
