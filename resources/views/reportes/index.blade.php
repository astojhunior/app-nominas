<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reportes del Sistema</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .report-card {
            display: block;
            padding: 16px;
            border: 2px solid #1e3a8a;
            border-radius: 12px;
            background: white;
            transition: all 0.2s ease-in-out;
        }

        .report-card:hover {
            transform: translateY(-3px);
            background: #f0f7ff;
            border-color: #1e40af;
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.2);
        }
    </style>
</head>

<body class="bg-[#0A4BD6] min-h-screen">

    <div class="max-w-7xl mx-auto p-8">

        <!-- TARJETA PRINCIPAL -->
        <div class="bg-white border-2 border-blue-600 rounded-2xl p-8 shadow-xl">

            <!-- BOTÓN VOLVER -->
            <a href="{{ route('admin.dashboard') }}"
               class="px-5 py-2 mb-6 inline-block bg-blue-700 text-white rounded-lg shadow hover:bg-blue-800 transition">
                 Volver
            </a>

            <h1 class="text-3xl font-extrabold text-blue-700 mb-6">
                Módulo de Reportes
            </h1>

            <!-- GRID GENERAL -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                <!-- =====================================
                    1. REPORTES POR PERSONAL
                ====================================== -->
                <div class="border border-blue-700 rounded-xl p-6 bg-white">

                    <h2 class="text-2xl font-bold text-blue-700 mb-6">
                        Reportes por Personal
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- ACTIVOS -->
                        <a href="{{ route('reportes.personal.activos') }}" class="report-card">
                            <p class="text-sm text-gray-600">Empleados activos</p>
                            <span class="font-bold text-blue-700">Ver reporte</span>
                        </a>

                        <!-- CESADOS -->
                        <a href="{{ route('reportes.personal.cesados') }}" class="report-card">
                            <p class="text-sm text-gray-600">Empleados cesados</p>
                            <span class="font-bold text-blue-700">Ver reporte</span>
                        </a>

                        <!-- POR ÁREA -->
                        <a href="{{ route('reportes.personal.area') }}" class="report-card">
                            <p class="text-sm text-gray-600">Personal por área</p>
                            <span class="font-bold text-blue-700">Ver reporte</span>
                        </a>

                        <!-- POR CARGO -->
                        <a href="{{ route('reportes.personal.cargo') }}" class="report-card">
                            <p class="text-sm text-gray-600">Personal por cargo</p>
                            <span class="font-bold text-blue-700">Ver reporte</span>
                        </a>

                    </div>
                </div>

            </div>
            <!-- FIN GRID -->

        </div>
        <!-- FIN TARJETA -->

    </div>

</body>
</html>
