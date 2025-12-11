<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte - Empleados Activos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#0A4BD6] min-h-screen">

<div class="max-w-7xl mx-auto p-8"
     x-data="{
        search: '',
        area: '',
        cargo: '',
     }">

    <!-- TARJETA -->
    <div class="bg-white border-2 border-blue-600 rounded-2xl p-8 shadow">

        <!-- TÍTULO + BOTÓN REGRESAR A LA DERECHA -->
        <div class="flex items-center justify-between mb-6">

            <h1 class="text-3xl font-extrabold text-blue-700">
                Reporte de Empleados Activos
            </h1>

            <a href="{{ route('reportes.personal.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 
                      text-white text-sm font-semibold rounded-lg transition shadow">
                <i data-lucide="arrow-left"></i>
                Regresar
            </a>

        </div>

        <!-- RESUMEN -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Total de Activos</p>
                <h3 class="text-3xl font-extrabold text-blue-700">
                    {{ $empleados->total() }}
                </h3>
            </div>

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Áreas Registradas</p>
                <h3 class="text-3xl font-extrabold text-blue-700">
                    {{ $areas->count() }}
                </h3>
            </div>

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Cargos Registrados</p>
                <h3 class="text-3xl font-extrabold text-blue-700">
                    {{ $cargos->count() }}
                </h3>
            </div>

        </div>

        <!-- FILTROS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">

            <!-- BUSCADOR -->
            <input type="text"
                   placeholder="Buscar por nombre o DNI..."
                   class="border border-blue-500 rounded-lg p-3"
                   x-model="search">

            <!-- ÁREA -->
            <select class="border border-blue-500 rounded-lg p-3"
                    x-model="area">
                <option value="">-- Todas las áreas --</option>
                @foreach($areas as $ar)
                    <option value="{{ $ar->id }}">{{ $ar->nombre }}</option>
                @endforeach
            </select>

            <!-- CARGO -->
            <select class="border border-blue-500 rounded-lg p-3"
                    x-model="cargo">
                <option value="">-- Todos los cargos --</option>
                @foreach($cargos as $cg)
                    <option value="{{ $cg->id }}">{{ $cg->cargo }}</option>
                @endforeach
            </select>

        </div>

        <!-- TABLA -->
<div class="overflow-x-auto mb-14">

    <!-- CONTENEDOR CON ALTURA MÁXIMA + SCROLL -->
    <div class="max-h-64 overflow-y-auto border border-blue-500 rounded-lg">

        <table class="min-w-full bg-white">
            <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="p-3 text-center">DNI</th>
                    <th class="p-3 text-center">Apellidos y Nombres</th>
                    <th class="p-3 text-center">Área</th>
                    <th class="p-3 text-center">Cargo</th>
                    <th class="p-3 text-center">Sueldo</th>
                    <th class="p-3 text-center">Ingreso</th>
                    <th class="p-3 text-center">Teléfono</th>
                </tr>
            </thead>

            <tbody>
                @foreach($empleados as $emp)
                    <tr class="border-b hover:bg-blue-50 transition"
                        x-show="
                            (search === '' ||
                             '{{ strtolower($emp->nombres . ' ' . $emp->apellidos) }}'.includes(search.toLowerCase()) ||
                             '{{ $emp->dni }}'.includes(search))
                            &&
                            (area === '' || '{{ $emp->contrato->area_id }}' === area)
                            &&
                            (cargo === '' || '{{ $emp->contrato->cargo_id }}' === cargo)
                        ">
                        <td class="p-3 text-center">{{ $emp->dni }}</td>
                        <td class="p-3 text-center">{{ $emp->apellidos }}, {{ $emp->nombres }}</td>
                        <td class="p-3 text-center">{{ $emp->contrato->area->nombre ?? '-' }}</td>
                        <td class="p-3 text-center">{{ $emp->contrato->cargo->cargo ?? '-' }}</td>
                        <td class="p-3 text-center">S/ {{ number_format($emp->contrato->sueldo, 2) }}</td>
                        <td class="p-3 text-center">{{ $emp->contrato->fecha_inicio }}</td>
                        <td class="p-3 text-center">{{ $emp->telefono ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
        <!-- PAGINACION -->
        <div class="mt-6 mb-14">
            {{ $empleados->links() }}
        </div>

        <!-- GRAFICOS -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="border border-blue-500 rounded-xl p-6">
                <h2 class="text-lg font-bold text-blue-700 mb-4">Distribución por Área</h2>
                <canvas id="graficoAreas"></canvas>
            </div>

            <div class="border border-blue-500 rounded-xl p-6">
                <h2 class="text-lg font-bold text-blue-700 mb-4">Distribución por Sexo</h2>
                <canvas id="graficoSexo"></canvas>
            </div>

        </div>

    </div>

</div>

<!-- GRAFICOS -->
<script>
document.addEventListener('alpine:init', () => {

    // EMPLEADOS POR ÁREA
    new Chart(document.getElementById('graficoAreas'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($areas->pluck('nombre')) !!},
            datasets: [{
                label: 'Empleados',
                data: {!! json_encode($conteoPorArea) !!},
                backgroundColor: '#0A4BD6'
            }]
        }
    });

    // SEXO
    new Chart(document.getElementById('graficoSexo'), {
        type: 'pie',
        data: {
            labels: ['Masculino', 'Femenino'],
            datasets: [{
                data: [
                    {{ $sexoMasculino }},
                    {{ $sexoFemenino }},
                ],
                backgroundColor: ['#1859D1', '#60A5FA']
            }]
        }
    });

});
</script>

</body>
</html>
