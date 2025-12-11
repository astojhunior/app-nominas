<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boletas por Bonos</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Elimina parpadeos de Alpine -->
    <style>[x-cloak]{ display:none!important; }</style>
</head>

<body class="bg-gradient-to-br from-blue-600 to-blue-800 min-h-screen text-white p-6">

<div class="max-w-6xl mx-auto bg-white text-gray-900 rounded-2xl p-8 shadow-lg"
     x-data="{ tab: 'bonos', openDelete: false, deleteId: null }" x-cloak>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-blue-700">Boletas por Bonos</h1>

        <a href="{{ route('boletas.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- Tabs con estilo profesional -->
    <div class="flex gap-10 border-b border-gray-200 mb-8">

        <!-- TAB 1 -->
        <button 
            @click="tab = 'bonos'"
            :class="tab === 'bonos' ? 'text-blue-700 font-semibold' : 'text-gray-500'"
            class="relative pb-2 transition">

            Bonos disponibles

            <span 
                x-show="tab === 'bonos'"
                x-transition
                class="absolute bottom-0 left-0 w-full h-[3px] bg-blue-700 rounded">
            </span>
        </button>

        <!-- TAB 2 -->
        <button 
            @click="tab = 'generadas'"
            :class="tab === 'generadas' ? 'text-blue-700 font-semibold' : 'text-gray-500'"
            class="relative pb-2 transition">

            Boletas generadas

            <span 
                x-show="tab === 'generadas'"
                x-transition
                class="absolute bottom-0 left-0 w-full h-[3px] bg-blue-700 rounded">
            </span>
        </button>

    </div>

    <!-- ============================
         TAB 1: BONOS DISPONIBLES
    ============================ -->
    <div x-show="tab === 'bonos'">

        <h2 class="text-2xl font-bold mb-4 text-blue-700">Bonos activos</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            @foreach($bonos as $bono)
                <a href="{{ route('boletas.bonos.confirmar', $bono->id) }}"
                   class="group border border-blue-100 rounded-2xl p-5 shadow-sm 
                          hover:shadow-lg hover:border-blue-500 hover:-translate-y-1 
                          transition-all duration-200 flex flex-col justify-between bg-white">

                    <div>
                        <h3 class="text-lg font-semibold text-blue-700 mb-2">
                            {{ $bono->nombre }}
                        </h3>

                        <p class="text-sm text-gray-600">
                            Monto: <strong>S/ {{ number_format($bono->monto, 2) }}</strong>
                        </p>

                        <p class="text-sm text-gray-600">
                            Fecha aplicación: {{ $bono->fecha_aplicacion }}
                        </p>
                    </div>

                    <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                        Generar boletas →
                    </span>

                </a>
            @endforeach

        </div>
    </div>

    <!-- ============================
         TAB 2: BOLETAS GENERADAS
    ============================ -->
    <div x-show="tab === 'generadas'" x-transition class="mt-6">

        <h2 class="text-2xl font-bold mb-4 text-blue-700">Boletas generadas</h2>

        <table class="w-full text-left bg-white rounded-xl overflow-hidden">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="px-4 py-2">Empleado</th>
                    <th class="px-4 py-2">Concepto</th>
                    <th class="px-4 py-2">Monto</th>
                    <th class="px-4 py-2">Fecha</th>
                    <th class="px-4 py-2 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $boletas = \App\Models\Boleta::where('tipo','bono')
                                ->orderBy('created_at','desc')->get();
                @endphp

                @foreach($boletas as $b)
                    <tr class="border-b hover:bg-blue-50">

                        <td class="px-4 py-2">{{ $b->empleado->nombres }} {{ $b->empleado->apellidos }}</td>

                        <td class="px-4 py-2">{{ $b->detalles->first()->concepto }}</td>

                        <td class="px-4 py-2">S/ {{ number_format($b->neto_pagar, 2) }}</td>

                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($b->fecha_generacion)->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-2 text-center flex items-center justify-center gap-4">

                            <!-- PDF -->
                           <a href="{{ route('boletas.bonos.pdf', $b->id) }}"
                            target="_blank"
                            class="text-blue-700 font-semibold hover:underline">
                            PDF
                            </a>

                            <!-- ELIMINAR -->
                            <button 
                                @click="openDelete = true; deleteId = {{ $b->id }}"
                                class="text-red-600 font-semibold hover:underline">
                                Eliminar
                            </button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>


    <!-- ============================
             MODAL ELIMINAR
    ============================ -->
    <div x-show="openDelete">

        <!-- Fondo Oscuro -->
        <div x-transition.opacity class="fixed inset-0 bg-black/40 z-40"></div>

        <!-- Modal -->
        <div x-transition class="fixed inset-0 flex items-center justify-center z-50">

            <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md text-gray-800">

                <h2 class="text-xl font-bold text-blue-700 mb-4">
                    ¿Estás seguro?
                </h2>

                <p class="text-gray-600 mb-6">
                    Esta acción eliminará la boleta seleccionada.  
                    No se puede deshacer.
                </p>

                <div class="flex justify-end gap-3">

                    <button @click="openDelete = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                        Cancelar
                    </button>

                    <form method="POST" :action="`/boletas/bonos/eliminar/${deleteId}`">
                        @csrf
                        @method('DELETE')

                        <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                            Eliminar
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

<script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>
