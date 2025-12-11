<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Liquidación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#1859D1] min-h-screen p-6">

<div class="bg-white max-w-5xl mx-auto p-8 rounded-2xl shadow">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">Resumen de Liquidación</h1>

        <a href="{{ route('boletas.liquidacion.index') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
        <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
        <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
        <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? '---' }}</p>

        <p><strong>Fecha de ingreso:</strong> {{ $ingresoFijo->format('Y-m-d') }}</p>
        <p><strong>Fecha de cese:</strong> {{ $ceseFijo->format('Y-m-d') }}</p>
        <p><strong>Antigüedad:</strong> {{ $antiguedadMeses }} meses, {{ $antiguedadDias }} días</p>
        <p><strong>Días trabajados reales:</strong> {{ $diasTrabajadosReales }}</p>
    </div>

    <h2 class="text-2xl font-bold text-blue-700 mt-8 mb-4">Detalle del cálculo</h2>

    <table class="w-full text-left border">
        <thead class="bg-blue-700 text-white">
        <tr>
            <th class="py-3 px-3">Concepto</th>
            <th class="py-3 px-3">Fórmula</th>
            <th class="py-3 px-3 text-right">Monto (S/.)</th>
        </tr>
        </thead>

        <tbody class="text-gray-700">

        <tr class="border-b">
            <td class="py-3 px-3">Vacaciones truncas</td>
            <td class="px-3">(Base/12 × {{ $antiguedadMeses }}) + (Base/360 × {{ $antiguedadDias }})</td>
            <td class="px-3 text-right">{{ number_format($vacaciones,2) }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">CTS trunca</td>
            <td class="px-3">(Base/12 × {{ $antiguedadMeses }}) + (Base/360 × {{ $antiguedadDias }})</td>
            <td class="px-3 text-right">{{ number_format($cts,2) }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Gratificación trunca</td>
            <td class="px-3">(Base/6 × {{ $mesesSemestre }})</td>
            <td class="px-3 text-right">{{ number_format($gratificacion,2) }}</td>
        </tr>

        <tr class="border-b">
            <td class="py-3 px-3">Días trabajados del último mes</td>
            <td class="px-3">(Sueldo/30 × {{ $diasUltimoMes }})</td>
            <td class="px-3 text-right">{{ number_format($remuneracionUltimoMes,2) }}</td>
        </tr>

        </tbody>
    </table>

    <p class="text-right text-3xl font-extrabold text-blue-700 mt-6">
        Total liquidación: S/ {{ number_format($totalLiquidacion,2) }}
    </p>

    <!-- FORMULARIO -->
    <form action="{{ route('boletas.liquidacion.generar') }}" method="POST" class="mt-6">
        @csrf

        <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
        <input type="hidden" name="total" value="{{ $totalLiquidacion }}">

        <!-- Fechas -->
        <input type="hidden" name="fecha_ingreso" value="{{ $ingresoFijo->toDateString() }}">
        <input type="hidden" name="fecha_cese" value="{{ $ceseFijo->toDateString() }}">
        <input type="hidden" name="antiguedad_texto" value="{{ $antiguedadMeses }} meses, {{ $antiguedadDias }} días">
        <input type="hidden" name="dias_trabajados_reales" value="{{ $diasTrabajadosReales }}">

        <!-- Base -->
        <input type="hidden" name="sueldo" value="{{ $sueldo }}">
        <input type="hidden" name="asignacion" value="{{ $asignacion }}">
        <input type="hidden" name="base" value="{{ $base }}">

        <!-- Vacaciones -->
        <input type="hidden" name="vac_meses" value="{{ $antiguedadMeses }}">
        <input type="hidden" name="vac_dias" value="{{ $antiguedadDias }}">
        <input type="hidden" name="monto_vacaciones" value="{{ $vacaciones }}">

        <!-- CTS -->
        <input type="hidden" name="cts_meses" value="{{ $antiguedadMeses }}">
        <input type="hidden" name="cts_dias" value="{{ $antiguedadDias }}">
        <input type="hidden" name="monto_cts" value="{{ $cts }}">

        <!-- Grati -->
        <input type="hidden" name="grati_meses" value="{{ $mesesSemestre }}">
        <input type="hidden" name="monto_grati" value="{{ $gratificacion }}">

        <!-- Último mes -->
        <input type="hidden" name="dias_ultimo_mes" value="{{ $diasUltimoMes }}">
        <input type="hidden" name="monto_dias_mes" value="{{ $remuneracionUltimoMes }}">

        <button class="bg-blue-700 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-800 transition font-bold">
            Guardar Boleta
        </button>
    </form>

</div>

</body>
</html>
