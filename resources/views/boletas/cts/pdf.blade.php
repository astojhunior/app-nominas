<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .titulo { font-size: 22px; font-weight: bold; color: #0A3AFF; margin-bottom: 20px; }
        .seccion-title { font-weight: bold; margin: 18px 0 6px; color:#0A3AFF; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th { background: #0A3AFF; color: white; padding: 8px; font-weight: bold; }
        td { border: 1px solid #ccc; padding: 6px; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .firma { margin-top: 60px; text-align: center; font-size: 12px; }
        .aviso { background: #FFF7CD; border-left: 4px solid #E0A800; padding: 10px; margin-bottom: 15px; }
    </style>
</head>

<body>

    <div class="titulo">Boleta de Depósito CTS</div>

    <!-- DATOS DEL EMPLEADO -->
    <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
    <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
    <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? '---' }}</p>
    <p><strong>Periodo CTS:</strong> {{ ucfirst($periodo) }} {{ $anio }}</p>

    <!-- AVISO LEGAL -->
    <div class="aviso">
        <strong>IMPORTANTE:</strong><br>
        El monto depositado por CTS mantiene su intangibilidad hasta el 
        <strong>31 de diciembre de 2026</strong>, según disposición legal vigente.
    </div>

    <!-- SECCION 1: PERIODO -->
    <div class="seccion-title">Periodo Computable</div>

    <table>
        <tr>
            <th>Detalle</th>
            <th class="right">Valor</th>
        </tr>

        <tr>
            <td>Inicio computable</td>
            <td class="right">{{ $inicio->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td>Fin computable</td>
            <td class="right">{{ $fin->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td>Meses completos trabajados</td>
            <td class="right">{{ $meses }}</td>
        </tr>

        <tr>
            <td>Días trabajados del mes incompleto</td>
            <td class="right">{{ $dias }}</td>
        </tr>
    </table>

    <!-- SECCION 2: BASE CTS -->
    <div class="seccion-title">Base de Cálculo CTS</div>

    <table>
        <tr>
            <th>Concepto</th>
            <th class="right">Monto (S/.)</th>
        </tr>

        <tr>
            <td>Sueldo básico</td>
            <td class="right">{{ number_format($sueldo,2) }}</td>
        </tr>

        <tr>
            <td>Asignación familiar</td>
            <td class="right">{{ number_format($asignacion,2) }}</td>
        </tr>

        <tr style="background: #EAF1FF;">
            <td class="bold">BASE CTS (Sueldo + Asignación Familiar)</td>
            <td class="right bold">{{ number_format($base,2) }}</td>
        </tr>
    </table>

    <!-- SECCION 3: FORMULA LEGAL -->
    <div class="seccion-title">Fórmula del Cálculo</div>

    <table>
        <tr>
            <th>Descripción</th>
            <th class="right">Monto (S/.)</th>
        </tr>

        <tr>
            <td>Base / 12 × Meses</td>
            <td class="right">{{ number_format(($base/12) * $meses,2) }}</td>
        </tr>

        <tr>
            <td>Base / 360 × Días</td>
            <td class="right">{{ number_format(($base/360) * $dias,2) }}</td>
        </tr>

        <tr style="background:#D7E5FF;">
            <td class="bold">TOTAL CTS</td>
            <td class="right bold">{{ number_format($totalCTS,2) }}</td>
        </tr>
    </table>

    <!-- SECCION 4: RESUMEN FINAL -->
    <div class="seccion-title">Resumen Final</div>

    <table>
        <tr>
            <td class="bold">Total CTS a depositar</td>
            <td class="right bold">{{ number_format($totalCTS,2) }}</td>
        </tr>
    </table>

    <div class="firma">
        _______________________________<br>
        FIRMA CONFORME DEL TRABAJADOR
    </div>

</body>
</html>
