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
        td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .firma { margin-top: 60px; text-align: center; font-size: 12px; }
    </style>
</head>

<body>

    <div class="titulo">Boleta de Liquidación de Beneficios Sociales</div>

    <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
    <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
    <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? 'Sin cargo' }}</p>

    <p><strong>Fecha de ingreso:</strong> {{ $boleta->fecha_ingreso }}</p>
    <p><strong>Fecha de cese:</strong> {{ $boleta->fecha_cese }}</p>
    <p><strong>Antigüedad total:</strong> {{ $boleta->antiguedad_texto }}</p>
    <p><strong>Días trabajados reales:</strong> {{ $boleta->dias_trabajados_reales }}</p>

    <div class="seccion-title">Remuneración computable</div>
    <table>
        <tr><th>Concepto</th><th class="right">Monto (S/.)</th></tr>

        <tr><td>Sueldo básico</td>
            <td class="right">{{ number_format($boleta->sueldo,2) }}</td></tr>

        <tr><td>Asignación familiar</td>
            <td class="right">{{ number_format($boleta->asignacion,2) }}</td></tr>

        <tr><td class="bold">Base de cálculo</td>
            <td class="right bold">{{ number_format($boleta->base,2) }}</td></tr>
    </table>

    <div class="seccion-title">Detalle del cálculo</div>
    <table>
        <tr>
            <th>Concepto</th>
            <th>Fórmula aplicada</th>
            <th class="right">Monto (S/.)</th>
        </tr>

        <tr>
            <td class="bold">Vacaciones truncas</td>
            <td>(Base/12 × {{ $boleta->vac_meses }}) + (Base/360 × {{ $boleta->vac_dias }})</td>
            <td class="right">{{ number_format($boleta->monto_vacaciones,2) }}</td>
        </tr>

        <tr>
            <td class="bold">CTS trunca</td>
            <td>(Base/12 × {{ $boleta->cts_meses }}) + (Base/360 × {{ $boleta->cts_dias }})</td>
            <td class="right">{{ number_format($boleta->monto_cts,2) }}</td>
        </tr>

        <tr>
            <td class="bold">Gratificación trunca</td>
            <td>(Base/6 × {{ $boleta->grati_meses }})</td>
            <td class="right">{{ number_format($boleta->monto_grati,2) }}</td>
        </tr>

        <tr>
            <td class="bold">Días trabajados del último mes</td>
            <td>(Sueldo/30 × {{ $boleta->dias_ultimo_mes }})</td>
            <td class="right">{{ number_format($boleta->monto_dias_mes,2) }}</td>
        </tr>

    </table>

    <div class="seccion-title">Resumen de liquidación</div>
    <table>
        <tr>
            <td class="bold">Total beneficios</td>
            <td class="right bold">{{ number_format($boleta->total_liquidacion,2) }}</td>
        </tr>
    </table>

    <div class="firma">
        _______________________________<br>
        FIRMA DEL TRABAJADOR
    </div>

</body>
</html>
