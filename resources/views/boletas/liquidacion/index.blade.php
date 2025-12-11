<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletas por Liquidación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>[x-cloak]{ display:none !important; }</style>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-7xl rounded-2xl shadow-xl p-8"
     x-data="{ 
        tab: '{{ request('tab','empleados') }}', 
        showDelete:false, 
        deleteId:null, 
        password:'' 
     }">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">Boletas por Liquidación</h1>

        <a href="{{ route('boletas.index') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- MENSAJES -->
    @if(session('success'))
        @php $esEliminacion = str_contains(strtolower(session('success')), 'elimin'); @endphp

        <div 
            x-data="{ show:true }" 
            x-show="show"
            x-transition.opacity.duration.900ms
            x-init="setTimeout(() => show=false, 3000)"
            class="p-3 rounded mb-4 shadow text-white {{ $esEliminacion ? 'bg-red-600' : 'bg-green-600' }}">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div 
            x-data="{ show:true }" 
            x-show="show"
            x-transition.opacity.duration.900ms
            x-init="setTimeout(() => show=false, 3000)"
            class="bg-red-600 text-white p-3 rounded mb-4 shadow">
            {{ session('error') }}
        </div>
    @endif

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <button @click="tab='empleados'"
                class="pb-3"
                :class="tab=='empleados'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Seleccionar Empleado
            </button>

            <button @click="tab='generadas'"
                class="pb-3"
                :class="tab=='generadas'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600'">
                Boletas Generadas
            </button>

        </div>
    </div>


    <!-- TAB EMPLEADOS -->
    <div x-show="tab=='empleados'" x-cloak>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Inicio</th>
                <th class="py-3">Cese</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">

            @forelse($empleados as $e)
                <tr class="border-b">
                    <td class="py-3">{{ $e->dni }}</td>
                    <td>{{ $e->apellidos }} {{ $e->nombres }}</td>
                    <td>{{ $e->contratoActual?->cargo?->cargo ?? '---' }}</td>
                    <td>{{ optional($e->contratoActual)->fecha_inicio ?? '---' }}</td>
                    <td>{{ $e->fecha_cese ?? '---' }}</td>

                    <td>
                        <a href="{{ route('boletas.liquidacion.confirmar', ['empleado_id' => $e->id]) }}"
                           class="bg-blue-700 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                            Calcular Liquidación
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-4 text-gray-500">No hay empleados en estado BAJA.</td>
                </tr>
            @endforelse

            </tbody>
        </table>

    </div>


        <!-- BUSCADOR + FILTRO DENTRO DEL TAB GENERADAS -->
<div x-show="tab=='generadas'" x-cloak>

    <form method="GET" class="mb-4 flex gap-3">
        <input type="hidden" name="tab" value="generadas">

        <input 
            type="text" 
            name="search" 
            placeholder="Buscar por nombre, DNI o cargo..."
            value="{{ request('search') }}"
            class="border p-2 rounded w-72"
        >

        <select name="mes" class="border p-2 rounded">
            <option value="">Mes...</option>
            @foreach($meses as $num => $nombre)
                <option value="{{ $num }}" {{ request('mes') == $num ? 'selected' : '' }}>
                    {{ $nombre }}
                </option>
            @endforeach
        </select>

        <select name="anio" class="border p-2 rounded">
            <option value="">Año...</option>
            @for($y=2020;$y<=date('Y');$y++)
                <option value="{{ $y }}" {{ request('anio') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>

        <button class="bg-blue-700 text-white px-4 rounded hover:bg-blue-800">
            Filtrar
        </button>
    </form>

    <!-- TABLA DE BOLETAS GENERADAS -->
    <table class="w-full text-center border rounded-lg overflow-hidden mt-4">
        <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Fecha</th>
                <th class="py-3">Acción</th>
            </tr>
        </thead>

        <tbody class="text-gray-700">

        @forelse($boletas as $b)
            <tr class="border-b">
                <td class="py-3">{{ $b->empleado->dni }}</td>

                <td class="py-3">{{ $b->empleado->apellidos }} {{ $b->empleado->nombres }}</td>

                <td class="py-3">
                    {{ $b->empleado->contratoActual?->cargo?->cargo ?? '---' }}
                </td>

                <td class="py-3">{{ $b->fecha_generacion->format('d/m/Y') }}</td>

                <td class="py-3">
                    <div class="flex items-center justify-center gap-6">
                        <a href="{{ route('boletas.liquidacion.pdf', $b->id) }}"
                           target="_blank"
                           class="text-blue-700 font-semibold hover:underline">
                            Ver PDF
                        </a>

                        <button 
                            @click="showDelete=true; deleteId={{ $b->id }}"
                            class="text-red-600 hover:underline font-semibold">
                            Eliminar
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="py-4 text-gray-500">No hay boletas generadas.</td>
            </tr>
        @endforelse

        </tbody>
    </table>

</div>



    <!-- MODAL ELIMINAR -->
    <div 
        x-show="showDelete"
        x-transition.opacity.duration.300ms
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white p-6 rounded-xl w-96 shadow-xl">

            <h2 class="text-xl font-bold text-red-600 mb-3">Confirmar eliminación</h2>

            <p class="mb-3">Ingrese su contraseña para eliminar la boleta.</p>

            <input type="password"
                   x-model="password"
                   class="w-full border p-2 rounded mb-4"
                   placeholder="Contraseña">

            <div class="flex justify-end gap-3">
                <button @click="showDelete=false; password=''"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancelar
                </button>

                <form :action="`{{ url('/boletas/liquidacion/delete') }}/${deleteId}`"
                      method="POST">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="password" :value="password">

                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

</body>
</html>
