<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletas por Fin de Mes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>[x-cloak]{display:none !important;}</style>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-8"
     x-data="{
        tab: '{{ request('tab','empleados') }}',
        deleteId: null,
        openDeleteModal(id) {
            this.deleteId = id;
            this.showDelete = true;
        },
        showDelete: false
     }"
     x-cloak>

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Boletas por Fin de Mes
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

    @if(session('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4 shadow-lg animate-fade">
            {{ session('error') }}
        </div>
    @endif

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <button @click="tab = 'empleados'"
                class="pb-3 transition-all duration-200"
                :class="tab=='empleados'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Seleccionar Empleado
            </button>

            <button @click="tab = 'periodo'"
                class="pb-3 transition-all duration-200"
                :class="tab=='periodo'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Seleccionar Mes
            </button>

            <button @click="tab = 'generadas'"
                class="pb-3 transition-all duration-200"
                :class="tab=='generadas'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Boletas Generadas
            </button>

        </div>
    </div>

    <!-- TAB EMPLEADOS -->
    <div x-show="tab=='empleados'">

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
                        <a href="{{ route('boletas.fin_mes.index', [
                                'tab' => 'periodo',
                                'empleado_id' => $e->id
                            ]) }}"
                           class="bg-blue-700 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                            Generar Boleta
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

    <!-- TAB PERIODO -->
    @if(request('empleado_id'))
    <div x-show="tab=='periodo'" x-cloak class="space-y-4">

        @php
            $empleado = $empleados->find(request('empleado_id'));
        @endphp

        <h2 class="text-xl font-bold text-blue-700">
            {{ $empleado->apellidos }} {{ $empleado->nombres }} ({{ $empleado->dni }})
        </h2>

        <form action="{{ route('boletas.fin_mes.confirmar') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="empleado_id" value="{{ request('empleado_id') }}">

            <div>
                <label class="font-semibold">Mes</label>
                <select name="periodo_mes" class="w-full border rounded-lg px-3 py-2 mt-1" required>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="font-semibold">Año</label>
                <input type="number" name="periodo_anio"
                       value="{{ now()->year }}"
                       class="w-full border rounded-lg px-3 py-2 mt-1" required>
            </div>

            <button class="bg-blue-700 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                Validar y Continuar
            </button>
        </form>

    </div>
    @endif

    <!-- TAB BOLETAS GENERADAS -->
    <div x-show="tab=='generadas'" x-cloak>

        <form method="GET" action="{{ route('boletas.fin_mes.index') }}" class="flex gap-4 mb-4">
            <input type="hidden" name="tab" value="generadas">

            <select name="mes" class="border rounded-lg px-3 py-2">
                <option value="">Mes</option>
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ request('mes')==$m ? 'selected':'' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>

            <input type="number" name="anio" value="{{ request('anio', now()->year) }}"
                   class="border rounded-lg px-3 py-2 w-32">

            <button class="bg-blue-700 text-white px-4 rounded-lg">
                Filtrar
            </button>
        </form>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">Empleado</th>
                <th class="py-3">Periodo</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @forelse($boletas as $b)
                <tr class="border-b">
                    <td class="py-3">
                        {{ $b->empleado->apellidos }} {{ $b->empleado->nombres }}
                    </td>
                    <td>{{ $b->periodo_mes }}/{{ $b->periodo_anio }}</td>
                    <td class="flex justify-center gap-4 py-3">

                        <a href="{{ route('boletas.fin_mes.pdf', $b->id) }}"
                           target="_blank"
                           class="text-blue-700 font-semibold hover:underline">
                            Ver PDF
                        </a>

                        <button
                            @click="openDeleteModal({{ $b->id }})"
                            class="text-red-600 font-semibold hover:underline">
                            Eliminar
                        </button>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-4 text-gray-500">No hay boletas generadas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    <!-- MODAL -->
    <div x-show="showDelete"
         class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center p-4"
         x-cloak>

        <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-xl">

            <h2 class="text-xl font-bold mb-4 text-gray-800">¿Estás seguro?</h2>

            <p class="text-gray-600 mb-6">
                Esta acción eliminará la boleta seleccionada. No se puede deshacer.
            </p>

            <div class="flex justify-end gap-4">

                <button @click="showDelete = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>

                <!-- FORMULARIO DE ELIMINACIÓN -->
               <form method="POST"
      :action="'/boletas/fin-mes/eliminar/' + deleteId 
                + '?tab=generadas&mes={{ request('mes') }}&anio={{ request('anio') }}'">

                    @csrf
                    @method('DELETE')

                    <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Eliminar
                    </button>
                </form>

            </div>

        </div>

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
