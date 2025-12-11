<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalle de Empleado</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#0A4BD6] min-h-screen">

<div class="max-w-7xl mx-auto p-8"
     x-data="{
        tab: '{{ $empleado ? 'ficha' : 'seleccionar' }}'
     }">

    <div class="bg-white border-2 border-blue-600 rounded-2xl p-8 shadow">

        <!-- BOTÓN REGRESAR -->
        <a href="{{ route('reportes.personal.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg mb-6 transition">
            ← Regresar
        </a>

        <h1 class="text-3xl font-extrabold text-blue-700 mb-6">
            Reporte Detallado del Personal
        </h1>

        <!-- TABS -->
        <div class="flex gap-6 border-b pb-3 mb-6 text-sm font-semibold">

            <button @click="tab = 'seleccionar'"
                :class="tab === 'seleccionar' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500'"
                class="pb-2">
                Seleccionar empleado
            </button>

            <button @click="tab = 'ficha'"
                :class="tab === 'ficha' ? 'text-blue-700 border-b-2 border-blue-700' : 'text-gray-500'"
                class="pb-2">
                Ficha del empleado
            </button>

        </div>

        <!-- TAB 1: LISTA DE EMPLEADOS -->
        <div x-show="tab === 'seleccionar'" x-transition>

            <input type="text"
                   x-model="search"
                   placeholder="Buscar empleado o DNI..."
                   class="border border-blue-500 rounded-lg p-3 mb-6 w-full">

            <table class="min-w-full bg-white rounded-lg overflow-hidden border border-blue-500">
                <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 text-center">DNI</th>
                    <th class="p-3 text-center">Empleado</th>
                    <th class="p-3 text-center">Área</th>
                    <th class="p-3 text-center">Cargo</th>
                    <th class="p-3 text-center">Acción</th>
                </tr>
                </thead>

                <tbody>
                @foreach($empleados as $e)
                    <tr class="border-b hover:bg-blue-50 transition">

                        <td class="p-3 text-center">{{ $e->dni }}</td>

                        <td class="p-3 text-center">
                            {{ $e->apellidos }}, {{ $e->nombres }}
                        </td>

                        <td class="p-3 text-center">
                            {{ $e->contrato->area->nombre ?? '-' }}
                        </td>

                        <td class="p-3 text-center">
                            {{ $e->contrato->cargo->cargo ?? '-' }}
                        </td>

                        <td class="p-3 text-center">
                            <a href="{{ route('reportes.personal.detalle', ['id' => $e->id]) }}"
                               class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
                                Ver detalle
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>


        <!-- TAB 2: FICHA DEL EMPLEADO -->
        <div x-show="tab === 'ficha'" x-transition>

            @if(!$empleado)
                <p class="text-gray-500 text-sm italic">
                    Selecciona un empleado en la pestaña anterior.
                </p>
            @else

            <!-- --- AQUÍ VA EXACTAMENTE LA FICHA COMPLETA QUE YA TIENES --- -->
            @include('reportes.personal.partials.ficha-empleado')

            @endif

        </div>

    </div>

</div>

</body>
</html>
