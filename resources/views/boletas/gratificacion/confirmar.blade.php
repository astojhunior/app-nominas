<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Boleta - Gratificación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-5xl rounded-2xl shadow-xl p-8">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-extrabold text-blue-700">
            Confirmación de Boleta de Gratificación
        </h1>

        <a href="{{ route('boletas.gratificacion.index', ['tab' => 'periodo']) }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- DATOS DEL EMPLEADO -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
        <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
        <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? '---' }}</p>
        <p><strong>Mes de gratificación:</strong> {{ $mesNombre }} {{ $anio }}</p>
    </div>

    <!-- BASE REMUNERACIÓN -->
    <h2 class="text-xl font-bold text-blue-700 mb-3">Remuneración computable</h2>

    <table class="w-full text-left border rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-700 text-white">
        <tr>
            <th class="py-3 px-3">Concepto</th>
            <th class="py-3 px-3 text-right">Monto (S/.)</th>
        </tr>
        </thead>

        <tbody class="text-gray-700">
        <tr class="border-b">
            <td class="py-3 px-3">Sueldo básico</td>
            <td class="py-3 px-3 text-right">{{ number_format($calculo['sueldo'], 2) }}</td>
        </tr>
        <tr class="border-b">
            <td class="py-3 px-3">Asignación familiar</td>
            <td class="py-3 px-3 text-right">{{ number_format($calculo['asignacion_familiar'], 2) }}</td>
        </tr>
        <tr class="border-b">
            <td class="py-3 px-3">Bonificación fija</td>
            <td class="py-3 px-3 text-right">{{ number_format($calculo['bonificacion_fija'], 2) }}</td>
        </tr>
        <tr>
            <td class="py-3 px-3 font-semibold">Remuneración computable</td>
            <td class="py-3 px-3 text-right font-semibold">
                {{ number_format($calculo['base_remuneracion'], 2) }}
            </td>
        </tr>
        </tbody>
    </table>

    <!-- DETALLE PERÍODO -->
    <h2 class="text-xl font-bold text-blue-700 mb-3">Detalle del período</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-gray-700">
        <div class="bg-gray-50 border rounded-lg p-4">
            <p><strong>Semestre:</strong><br>
                @if($mesGrati == 7)
                    01/01/{{ $anio }} al 30/06/{{ $anio }}
                @else
                    01/07/{{ $anio }} al 31/12/{{ $anio }}
                @endif
            </p>
            <p class="mt-2">
                <strong>Tiempo efectivo:</strong><br>
                {{ $calculo['inicio_efectivo']->format('d/m/Y') }}
                al
                {{ $calculo['fin_efectivo']->format('d/m/Y') }}
            </p>
        </div>

        <div class="bg-gray-50 border rounded-lg p-4">
            <p><strong>Días trabajados:</strong> {{ $calculo['dias_trabajados'] }}</p>
            <p class="mt-2">
                <strong>Meses equivalentes:</strong>
                {{ $calculo['meses_equivalentes'] }}
                mes(es) + {{ $calculo['dias_residuo'] }} día(s)
            </p>
        </div>
    </div>

    <!-- RESUMEN DE INGRESOS -->
    <h2 class="text-xl font-bold text-blue-700 mb-3">Resumen de ingresos</h2>

    <table class="w-full text-left border rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-700 text-white">
        <tr>
            <th class="py-3 px-3">Concepto</th>
            <th class="py-3 px-3 text-right">Monto (S/.)</th>
        </tr>
        </thead>

        <tbody class="text-gray-700">
        <tr class="border-b">
            <td class="py-3 px-3">Gratificación legal {{ $mesNombre }}</td>
            <td class="py-3 px-3 text-right">{{ number_format($calculo['gratificacion'], 2) }}</td>
        </tr>
        <tr class="border-b">
            <td class="py-3 px-3">Bonificación extraordinaria Essalud 9%</td>
            <td class="py-3 px-3 text-right">{{ number_format($calculo['bonificacion_essalud'], 2) }}</td>
        </tr>
        <tr>
            <td class="py-3 px-3 font-semibold">Total ingresos</td>
            <td class="py-3 px-3 text-right font-semibold">
                {{ number_format($calculo['total_ingresos'], 2) }}
            </td>
        </tr>
        </tbody>
    </table>

    <!-- NETO -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-green-900">
        <p class="font-semibold text-lg">
            Neto a pagar: S/. {{ number_format($calculo['neto_pagar'], 2) }}
        </p>
        <p class="text-sm mt-1">
            (No se realizan descuentos por AFP/ONP sobre la gratificación).
        </p>
    </div>

    <!-- BOTÓN GENERAR -->
    <form action="{{ route('boletas.gratificacion.generar') }}" method="POST">
        @csrf
        <input type="hidden" name="empleado_id"  value="{{ $empleado->id }}">
        <input type="hidden" name="periodo_mes"  value="{{ $mesGrati }}">
        <input type="hidden" name="periodo_anio" value="{{ $anio }}">

        <button
            class="bg-blue-700 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-800 transition w-full">
            Generar Boleta de Gratificación
        </button>
    </form>

</div>

</body>
</html>
