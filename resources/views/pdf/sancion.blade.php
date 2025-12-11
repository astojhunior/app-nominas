<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sanción Nº {{ $s->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            padding: 20px 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #1a237e;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 18px;
            margin-bottom: 8px;
            color: #1a237e;
            border-bottom: 1px solid #1a237e;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 35%;
        }

        .box {
            border: 1px solid #777;
            padding: 10px;
            min-height: 60px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
        }

        .signature {
            margin-top: 60px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 250px;
            margin: 0 auto;
            padding-top: 5px;
        }

        .date {
            text-align: right;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>

<body>
<div class="container">

    <!-- ENCABEZADO -->
    <div class="header">
        <h2>REGISTRO DE SANCIÓN</h2>
        <p><b>Sanción Nº:</b> {{ $s->id }}</p>
    </div>

    <!-- DATOS DEL EMPLEADO -->
    <div class="section-title">Datos del empleado</div>

    <table>
        <tr>
            <td class="label">Apellidos y nombres:</td>
            <td>{{ $s->empleado->apellidos }} {{ $s->empleado->nombres }}</td>
        </tr>
        <tr>
            <td class="label">DNI:</td>
            <td>{{ $s->empleado->dni }}</td>
        </tr>
        <tr>
            <td class="label">Cargo:</td>
            <td>{{ $s->empleado->contratoActual?->cargo?->cargo ?? '---' }}</td>
        </tr>
    </table>

    <!-- DETALLE DE SANCIÓN -->
    <div class="section-title">Detalle de sanción</div>

    <table>
        <tr>
            <td class="label">Tipo de sanción:</td>
            <td>{{ $s->tipo->nombre }}</td>
        </tr>

        <tr>
            <td class="label">Fecha de aplicación:</td>
            <td>{{ $s->fecha_aplicacion }}</td>
        </tr>

        @if($s->dias_suspension)
            <tr>
                <td class="label">Días de suspensión:</td>
                <td>{{ $s->dias_suspension }}</td>
            </tr>
            <tr>
                <td class="label">Fecha inicio suspensión:</td>
                <td>{{ $s->fecha_inicio_suspension }}</td>
            </tr>
            <tr>
                <td class="label">Fecha fin suspensión:</td>
                <td>{{ $s->fecha_fin_suspension }}</td>
            </tr>
        @endif

        <tr>
            <td class="label">Estado actual:</td>
            <td style="text-transform: uppercase;">
                {{ $s->estado }}
            </td>
        </tr>
    </table>

    <!-- MOTIVO -->
    <div class="section-title">Motivo / Observaciones</div>
    <div class="box">
        {{ $s->motivo ?? '---' }}
    </div>

    <!-- FECHA DOCUMENTO -->
    <div class="date">
        Emitido el {{ now()->format('d/m/Y') }}
    </div>

    <!-- FIRMAS -->
    <div class="signature">
        <div class="signature-line"></div>
        <p>Jefe de Recursos Humanos</p>
    </div>

</div>
</body>
</html>
