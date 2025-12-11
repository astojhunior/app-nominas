<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reportes por Personal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-7xl mx-auto p-8">

        <!-- TARJETA PRINCIPAL -->
        <div class="bg-white border-2 border-blue-600 rounded-2xl p-8 shadow">

            <h1 class="text-3xl font-extrabold text-blue-700 mb-6">
                Reportes por Personal
            </h1>

            <!-- GRID DE TARJETAS -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <!-- EMPLEADOS ACTIVOS -->
                <a href="{{ route('reportes.personal.activos') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Empleados activos</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- EMPLEADOS CESADOS -->
                <a href="{{ route('reportes.personal.cesados') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Empleados cesados</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- PERSONAL POR ÁREA -->
                <a href="{{ route('reportes.personal.area') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Personal por área</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- PERSONAL POR CARGO -->
                <a href="{{ route('reportes.personal.cargo') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Personal por cargo</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- DETALLE EMPLEADO -->
                <a href="{{ route('reportes.personal.detalle') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Detalle del empleado</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- HISTORIAL LABORAL -->
                <a href="{{ route('reportes.personal.historial') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Historial laboral</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- HISTORIAL DE CONTRATOS -->
                <a href="{{ route('reportes.personal.contratos') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Historial de contratos</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- MOVIMIENTOS -->
                <a href="{{ route('reportes.personal.movimientos') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Movimientos del empleado</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- SUELDO BASE -->
                <a href="{{ route('reportes.personal.sueldo') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Sueldo base actual</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- ÚLTIMA BOLETA -->
                <a href="{{ route('reportes.personal.ultima_boleta') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Última boleta emitida</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- TOTAL DE SANCIONES -->
                <a href="{{ route('reportes.personal.total_sanciones') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Total de sanciones</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

                <!-- ÚLTIMA SANCIÓN -->
                <a href="{{ route('reportes.personal.ultima_sancion') }}"
                   class="block border border-blue-500 rounded-xl p-6 hover:bg-blue-50 transition">
                    <p class="text-sm text-gray-600">Última sanción registrada</p>
                    <h3 class="text-lg font-bold text-blue-700 mt-1">Ver reporte</h3>
                </a>

            </div>

        </div>
    </div>

</body>
</html>
