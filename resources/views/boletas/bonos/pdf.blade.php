<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 35px;
            font-size: 13px;
            color: #1f2937;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2563eb;
        }

        .titulo {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 8px;
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th {
            background: #2563eb;
            color: white;
            padding: 8px;
            font-size: 13px;
            border: 1px solid #1e40af;
            text-align: center;
        }

        td {
            padding: 8px;
            border: 1px solid #9ca3af;
        }

        .total {
            font-weight: bold;
            background: #f3f4f6;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="header">
        <div class="titulo">Boleta por Bono</div>
        <div style="font-size: 12px; color: #374151;">
            Generado automáticamente por el sistema de Nómina
        </div>
    </div>

    <!-- DATOS DEL EMPLEADO -->
    <div>
        <div class="section-title">Datos del Empleado</div>

        <table>
            <tr>
                <td><strong>Empleado:</strong></td>
                <td>{{ $empleado->nombres }} {{ $empleado->apellidos }}</td>
            </tr>
            <tr>
                <td><strong>DNI:</strong></td>
                <td>{{ $empleado->dni }}</td>
            </tr>
            <tr>
                <td><strong>Fecha de generación:</strong></td>
                <td>{{ \Carbon\Carbon::parse($boleta->fecha_generacion)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- DETALLE -->
    <div>
        <div class="section-title">Detalle del Bono</div>

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Motivo</th>
                    <th>Monto</th>
                </tr>
            </thead>

            <tbody>
                @foreach($detalles as $d)
                <tr>
                    <td>{{ $d->concepto }}</td>
                    <td>{{ $d->motivo ?? '---' }}</td>
                    <td style="text-align:right">S/ {{ number_format($d->monto, 2) }}</td>
                </tr>
                @endforeach

                <tr class="total">
                    <td colspan="2" style="text-align:right"><strong>Total Neto a Pagar</strong></td>
                    <td style="text-align:right"><strong>S/ {{ number_format($boleta->neto_pagar, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento generado automáticamente — No requiere firma
    </div>

</body>
</html>
