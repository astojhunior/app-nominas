<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte - Personal por Cargo</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#0A4BD6] min-h-screen">

<div class="max-w-7xl mx-auto p-8"
     x-data="{
        search: '',
        cargo: '',
        estado: '',
     }">

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="bg-white border-2 border-blue-600 rounded-2xl p-8 shadow">

        <!-- TÍTULO + REGRESAR -->
        <div class="flex items-center justify-between mb-6">

            <h1 class="text-3xl font-extrabold text-blue-700">
                Reporte de Personal por Cargo
            </h1>

            <a href="{{ route('reportes.personal.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 
                      hover:bg-blue-700 text-white text-sm font-semibold 
                      rounded-lg transition shadow">
                <i data-lucide="arrow-left"></i> Regresar
            </a>

        </div>

        <!-- RESUMEN -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Total de Empleados</p>
                <h3 class="text-3xl font-extrabold text-blue-700">
                    {{ $totalEmpleados }}
                </h3>
            </div>

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Total de Cargos</p>
                <h3 class="text-3xl font-extrabold text-blue-700">
                    {{ $cargos->count() }}
                </h3>
            </div>

            <div class="border border-blue-500 p-6 rounded-xl text-center">
                <p class="text-sm text-gray-600">Cargo con más personal</p>
                <h3 class="text-lg font-extrabold text-blue-700">
                    {{ $cargoMayor ?? '-' }}
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

            <!-- SELECT CARGO -->
            <select class="border border-blue-500 rounded-lg p-3"
                    x-model="cargo">
                <option value="">-- Todos los cargos --</option>
                @foreach($cargos as $cg)
                    <option value="{{ $cg->id }}">{{ $cg->cargo }}</option>
                @endforeach
            </select>

            <!-- SELECT ESTADO -->
            <select class="border border-blue-500 rounded-lg p-3"
                    x-model="estado">
                <option value="">-- Todos los estados --</option>
                <option value="activo">Activo</option>
                <option value="baja">Baja</option>
            </select>

        </div>

<!-- TABLA (ARRIBA) -->
<div class="overflow-x-auto mb-14">

    <!-- CONTENEDOR CON ALTURA LIMITADA A 2 FILAS + SCROLL -->
    <div class="max-h-40 overflow-y-auto border border-blue-500 rounded-lg">

        <table class="min-w-full bg-white">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="p-3 text-center">Cargo</th>
                <th class="p-3 text-center">DNI</th>
                <th class="p-3 text-center">Empleado</th>
                <th class="p-3 text-center">Área</th>
                <th class="p-3 text-center">Sueldo</th>
                <th class="p-3 text-center">Estado</th>
            </tr>
            </thead>

            <tbody>
            @foreach($empleados as $emp)
                <tr class="border-b hover:bg-blue-50 transition"
                    x-show="

                        // BÚSQUEDA
                        (search === '' ||
                         '{{ strtolower($emp->nombres . ' ' . $emp->apellidos) }}'
                            .includes(search.toLowerCase()) ||
                         '{{ $emp->dni }}'.includes(search))

                        // FILTRO CARGO
                        &&
                        (cargo === '' || '{{ $emp->contrato->cargo_id }}' === cargo)

                        // FILTRO ESTADO
                        &&
                        (estado === '' || '{{ strtolower($emp->estado) }}' === estado)
                    ">

                    <td class="p-3 text-center">
                        {{ $emp->contrato->cargo->cargo ?? '-' }}
                    </td>

                    <td class="p-3 text-center">
                        {{ $emp->dni }}
                    </td>

                    <td class="p-3 text-center">
                        {{ $emp->apellidos }}, {{ $emp->nombres }}
                    </td>

                    <td class="p-3 text-center">
                        {{ $emp->contrato->area->nombre ?? '-' }}
                    </td>

                    <td class="p-3 text-center">
                        S/ {{ number_format($emp->contrato->sueldo, 2) }}
                    </td>

                    <td class="p-3 text-center">
                        {{ ucfirst($emp->estado) }}
                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>

    </div>
</div>


        <!-- PAGINACIÓN -->
        <div class="mt-6 mb-14">
            {{ $empleados->links() }}
        </div>

        <!-- GRÁFICO (ABAJO) -->
        <div class="border border-blue-500 rounded-xl p-6">
            <h2 class="text-lg font-bold text-blue-700 mb-4">
                Empleados por Cargo
            </h2>
            <canvas id="graficoCargo"></canvas>
        </div>

    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {

    new Chart(document.getElementById('graficoCargo'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($cargos->pluck('cargo')) !!},
            datasets: [{
                label: 'Cantidad de Empleados',
                data: {!! json_encode($conteoPorCargo) !!},
                backgroundColor: '#0A4BD6',
                borderWidth: 1
            }]
        }
    });

});
</script>

</body>
</html>
