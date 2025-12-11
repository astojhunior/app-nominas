<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletas CTS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- EVITAR PARPADEOS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-8"
     x-data="{ tab: '{{ request('tab','empleados') }}' }">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Boletas por CTS
        </h1>

        <a href="{{ route('boletas.index') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- MENSAJES -->
    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 shadow-lg animate-fade">
            {{ session('success') }}
        </div>
    @endif

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <!-- TAB EMPLEADOS -->
            <button @click="tab = 'empleados'"
                class="pb-3 transition-all duration-200"
                :class="tab=='empleados'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Seleccionar Empleado
            </button>

            <!-- TAB PERIODO -->
            <button
                @click="tab = 'periodo'"
                class="pb-3 transition-all duration-200"
                :class="tab=='periodo'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'"
                :disabled="!{{ request('empleado_id') ? 'true' : 'false' }}">

                Seleccionar Periodo
            </button>

            <!-- TAB GENERADAS -->
            <button
                @click="tab = 'generadas'"
                class="pb-3 transition-all duration-200"
                :class="tab=='generadas'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Boletas Generadas
            </button>

        </div>
    </div>

    <!-- TAB 1: EMPLEADOS -->
    <div x-show="tab=='empleados'" x-cloak>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @foreach($empleados as $e)
                <tr class="border-b">
                    <td class="py-3">{{ $e->dni }}</td>
                    <td>{{ $e->apellidos }} {{ $e->nombres }}</td>
                    <td>{{ $e->contratoActual?->cargo?->cargo ?? '---' }}</td>
                    <td>
                        <a href="{{ route('boletas.cts.index', [
                                'tab' => 'periodo',
                                'empleado_id' => $e->id
                            ]) }}"
                           class="bg-blue-700 text-white px-4 py-2 rounded-lg shadow
                                  hover:bg-blue-800 transition">
                            Seleccionar
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

    <!-- TAB 2: PERIODO CTS -->
    @if(request('empleado_id'))
    <div x-show="tab=='periodo'" x-cloak class="space-y-4">

        @php
            $empleado = $empleados->find(request('empleado_id'));
        @endphp

        <h2 class="text-xl font-bold text-blue-700">
            {{ $empleado->apellidos }} {{ $empleado->nombres }}
            ({{ $empleado->dni }})
        </h2>

        <form action="{{ route('boletas.cts.confirmar') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="empleado_id" value="{{ request('empleado_id') }}">

            <div>
                <label class="font-semibold">Periodo de CTS</label>
                <select name="periodo" class="w-full border rounded-lg px-3 py-2 mt-1" required>
                    <option value="mayo">CTS de Mayo</option>
                    <option value="noviembre">CTS de Noviembre</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Año</label>
                <input type="number" name="anio"
                       value="{{ now()->year }}"
                       class="w-full border rounded-lg px-3 py-2 mt-1" required>
            </div>

            <button class="bg-blue-700 text-white px-6 py-2 rounded-lg shadow
                           hover:bg-blue-800 transition">
                Validar y Continuar
            </button>
        </form>

    </div>
    @endif

    <!-- TAB 3: BOLETAS GENERADAS -->
    <div x-show="tab=='generadas'" x-cloak>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">Empleado</th>
                <th class="py-3">Periodo</th>
                <th class="py-3">Año</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @foreach($boletas as $b)
                <tr class="border-b">
                    <td class="py-3">
                        {{ $b->empleado->apellidos }} {{ $b->empleado->nombres }}
                    </td>
                    <td>{{ ucfirst($b->periodo_mes) }}</td>
                    <td>{{ $b->periodo_anio }}</td>
                    <td>
                        <a href="{{ route('boletas.cts.pdf', $b->id) }}"
                           class="text-blue-700 font-semibold hover:underline">
                            Ver PDF
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

</div>

<style>
    .animate-fade {
        animation: fade 0.5s ease-in-out;
    }
    @keyframes fade {
        from { opacity: 0; transform: translateY(-5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

</body>
</html>
