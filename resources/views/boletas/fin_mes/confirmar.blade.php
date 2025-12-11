<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Boleta - Fin de Mes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>[x-cloak]{ display:none !important; }</style>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-5xl rounded-2xl shadow-xl p-8"
     x-data="{
        tieneDescuentos: false,
        descuentos: [{ motivo: '', monto: '' }]
     }">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-extrabold text-blue-700">
            Confirmación de Boleta de Fin de Mes
        </h1>

        <a href="{{ route('boletas.fin_mes.index', ['empleado_id' => $empleado->id, 'tab'=>'periodo']) }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- DATOS DEL EMPLEADO -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p><strong>Empleado:</strong> {{ $empleado->apellidos }} {{ $empleado->nombres }}</p>
        <p><strong>DNI:</strong> {{ $empleado->dni }}</p>
        <p><strong>Cargo:</strong> {{ $empleado->contratoActual?->cargo?->cargo ?? '---' }}</p>
        <p><strong>Periodo:</strong> {{ $mesNombre }} {{ $periodo_anio }}</p>
    </div>

    <!-- RESUMEN -->
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
            <td class="py-3 px-3">Sueldo Base</td>
            <td class="py-3 px-3 text-right">{{ number_format($sueldo_base, 2) }}</td>
        </tr>

        @if($asignacion_familiar > 0)
            <tr class="border-b">
                <td class="py-3 px-3">Asignación Familiar</td>
                <td class="py-3 px-3 text-right">{{ number_format($asignacion_familiar, 2) }}</td>
            </tr>
        @endif

        </tbody>
    </table>
<form action="{{ route('boletas.fin_mes.generar') }}" method="POST">
    @csrf

    <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
    <input type="hidden" name="periodo_mes" value="{{ $periodo_mes }}">
    <input type="hidden" name="periodo_anio" value="{{ $periodo_anio }}">

    <!-- ❗ DESCUENTOS OPCIONALES -->
    <div class="mb-6">
        <label class="flex items-center gap-3 text-lg font-semibold text-red-600 cursor-pointer">
            <input type="checkbox" x-model="tieneDescuentos" class="w-5 h-5">
            ¿Tiene descuentos adicionales?
        </label>
    </div>

    <!-- CAMPOS DE DESCUENTOS (DENTRO DEL FORMULARIO) -->
    <div class="space-y-4 mb-6" x-show="tieneDescuentos" x-transition x-cloak>
        <template x-for="(item, index) in descuentos" :key="index">
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="font-semibold">Motivo</label>
                    <input type="text"
                           class="w-full border rounded-lg px-3 py-2 mt-1"
                           x-model="item.motivo"
                           :name="`descuentos[${index}][motivo]`"
                           placeholder="Ej: vaso roto">
                </div>

                <div>
                    <label class="font-semibold">Monto (S/.)</label>
                    <input type="number" step="0.01"
                           class="w-full border rounded-lg px-3 py-2 mt-1"
                           x-model="item.monto"
                           :name="`descuentos[${index}][monto]`"
                           placeholder="Ej: 25.50">
                </div>

            </div>
        </template>

        <button type="button"
                class="bg-red-600 text-white px-4 py-2 rounded-lg shadow hover:bg-red-700 transition"
                @click="descuentos.push({ motivo: '', monto: '' })">
            + Agregar otro descuento
        </button>
    </div>

    <!-- BOTÓN GENERAR -->
    <button
        class="bg-blue-700 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-800 transition w-full">
        Generar Boleta
    </button>

</form>

</div>

</body>
</html>
