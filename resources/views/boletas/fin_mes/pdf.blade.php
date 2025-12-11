<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .titulo { font-size: 20px; font-weight: bold; color: #0A3AFF; margin-bottom: 20px; }
        .seccion-title { font-weight: bold; margin: 18px 0 6px; color:#0A3AFF; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #0A3AFF; color: white; padding: 8px; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 6px; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .firma { margin-top: 60px; text-align: center; font-size: 12px; }
    </style>
</head>

<body>

    <div class="titulo">Boleta de Pago - Fin de Mes</div>

    <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
    <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
    <p><strong>Cargo:</strong> {{ $empleado->contratoActual->cargo->cargo ?? 'Sin cargo' }}</p>
    <p><strong>Mes:</strong> {{ $mesNombre }} {{ $boleta->periodo_anio }}</p>

    <div class="seccion-title">INGRESOS</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>

        <tr>
            <td>Sueldo (Quincena 40%)</td>
            <td class="right">{{ number_format($quincena, 2) }}</td>
        </tr>

        <tr>
            <td>Sueldo (Fin de mes 60%)</td>
            <td class="right">{{ number_format($finMes, 2) }}</td>
        </tr>

        @foreach($ingresos_adicionales as $item)
        <tr>
            <td>{{ $item['concepto'] }}</td>
            <td class="right">{{ number_format($item['monto'],2) }}</td>
        </tr>
        @endforeach

    </table>

    <div class="seccion-title">DESCUENTOS</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>

        <tr>
            <td>Aporte AFP</td>
            <td class="right">{{ number_format($afp_aporte,2) }}</td>
        </tr>

        @foreach($descuentos_adicionales as $d)
        <tr>
            <td>{{ $d['motivo'] }}</td>
            <td class="right">{{ number_format($d['monto'],2) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="seccion-title">APORTES</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>
        <tr>
            <td>Essalud</td>
            <td class="right">{{ number_format($essalud,2) }}</td>
        </tr>
    </table>

    <div class="seccion-title">RESUMEN</div>
    <table>
        <tr>
            <td class="bold">Total Ingresos</td>
            <td class="right">{{ number_format($total_ingresos,2) }}</td>
        </tr>

        <tr>
            <td class="bold">Total Descuentos</td>
            <td class="right">{{ number_format($total_descuentos,2) }}</td>
        </tr>

        <tr>
            <td class="bold">Neto a Pagar</td>
            <td class="right bold">{{ number_format($neto,2) }}</td>
        </tr>
    </table>

    <div class="firma">
        _______________________________<br>
        FIRMA CONFORME
    </div>

</body>
</html>
