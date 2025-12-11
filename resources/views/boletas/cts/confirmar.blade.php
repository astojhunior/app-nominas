<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar CTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl p-8">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-extrabold text-blue-700">
            Confirmación de Cálculo CTS
        </h1>

        <a href="{{ route('boletas.cts.index', ['tab'=>'periodo', 'empleado_id'=>$empleado->id]) }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>


    <!-- DATOS DEL EMPLEADO -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
        <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
        <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? '---' }}</p>
        <p><strong>Periodo CTS:</strong> {{ ucfirst($periodo) }} {{ $anio }}</p>
    </div>

    <!-- AVISO -->
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 mb-6 rounded shadow">
        <p class="font-semibold">IMPORTANTE:</p>
        <p class="text-sm">
            La CTS depositada mantiene su intangibilidad hasta el
            <strong>31 de diciembre de 2026</strong>, según normativa vigente.
        </p>
    </div>

    <!-- RESUMEN -->
    <h2 class="text-xl font-bold text-blue-700 mb-3">Resumen del Periodo</h2>

    <table class="w-full text-left border rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-700 text-white">
        <tr>
            <th class="py-3 px-3">Concepto</th>
            <th class="py-3 px-3 text-right">Valor</th>
        </tr>
        </thead>

        <tbody class="text-gray-700">

        <tr class="border-b">
            <td class="py-3 px-3">Inicio computable</td>
            <td class="py-3 px-3 text-right">{{ $inicio->format('d-m-Y') }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Fin computable</td>
            <td class="py-3 px-3 text-right">{{ $fin->format('d-m-Y') }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Meses completos trabajados</td>
            <td class="py-3 px-3 text-right">{{ $meses }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Días trabajados (mes incompleto)</td>
            <td class="py-3 px-3 text-right">{{ $dias }}</td>
        </tr>

        </tbody>
    </table>

    <!-- BASE Y CALCULOS -->
    <h2 class="text-xl font-bold text-blue-700 mb-3">Cálculo Legal</h2>

    <table class="w-full text-left border rounded-lg overflow-hidden mb-6">
        <thead class="bg-blue-700 text-white">
        <tr>
            <th class="py-3 px-3">Detalle</th>
            <th class="py-3 px-3 text-right">Monto (S/.)</th>
        </tr>
        </thead>

        <tbody class="text-gray-700">

        <tr class="border-b">
            <td class="py-3 px-3">Sueldo</td>
            <td class="py-3 px-3 text-right">{{ number_format($sueldo,2) }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Asignación Familiar</td>
            <td class="py-3 px-3 text-right">{{ number_format($asignacion,2) }}</td>
        </tr>

        <tr class="border-b bg-blue-50">
            <td class="py-3 px-3 font-bold">Base CTS (Sueldo + AF)</td>
            <td class="py-3 px-3 text-right font-bold">{{ number_format($base,2) }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Base / 12 × Meses completos</td>
            <td class="py-3 px-3 text-right">
                {{ number_format(($base/12) * $meses,2) }}
            </td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Base / 360 × Días</td>
            <td class="py-3 px-3 text-right">
                {{ number_format(($base/360) * $dias,2) }}
            </td>
        </tr>

        <tr class="border-b bg-blue-100 font-bold text-blue-700 text-lg">
            <td class="py-3 px-3">TOTAL CTS</td>
            <td class="py-3 px-3 text-right">
                {{ number_format($totalCTS,2) }}
            </td>
        </tr>

        </tbody>
    </table>

    <!-- BOTÓN GENERAR -->
    <form action="{{ route('boletas.cts.generar') }}" method="POST">
        @csrf

        <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
        <input type="hidden" name="periodo" value="{{ $periodo }}">
        <input type="hidden" name="anio" value="{{ $anio }}">
        <input type="hidden" name="total" value="{{ $totalCTS }}">

        <button
            class="bg-blue-700 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-800 transition w-full text-lg font-semibold">
            Generar Boleta CTS
        </button>
    </form>

</div>

</body>
</html>
