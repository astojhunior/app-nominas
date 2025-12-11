<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Boletas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-8">

    <!-- Encabezado -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-blue-700">
                Gestión de Boletas
            </h1>
            <p class="text-gray-500 mt-1">
                Seleccione el tipo de boleta o acción que desea gestionar.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- Tarjetas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Boletas por fin de mes -->
        <a href="{{ route('boletas.fin_mes.index') }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Boletas por fin de mes
                </h2>
                <p class="text-sm text-gray-600">
                    Generar boletas mensuales con sueldo, quincenas, asignación y descuentos.
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

        <!-- Boletas por Gratificación -->
        <a href="{{ route('boletas.gratificacion.index') }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Boletas por gratificación
                </h2>
                <p class="text-sm text-gray-600">
                    Generar boletas de gratificación proporcional según días trabajados.
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

        <!-- Boletas por CTS -->
        <a href="{{ route('boletas.cts.index') }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Boletas por CTS
                </h2>
                <p class="text-sm text-gray-600">
                    Calcular y generar boletas CTS según periodo legal (mayo o noviembre).
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

        <!-- Boletas por Liquidación -->
        <a href="{{ route('boletas.liquidacion.index') }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Boletas por liquidación
                </h2>
                <p class="text-sm text-gray-600">
                    Calcular y generar boletas de liquidación para empleados cesados.
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

        <!-- Crear Bono -->
        <a href="{{ route('bonos.index', ['tab' => 'registrar']) }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Crear bono
                </h2>
                <p class="text-sm text-gray-600">
                    Registrar un bono para empleados, cargos o todo el personal.
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

        <!-- Boletas por Bonos -->
        <a href="{{ route('boletas.bonos.index') }}"
           class="group border border-blue-100 rounded-2xl p-5 shadow-sm
                  hover:shadow-lg hover:border-blue-500 hover:-translate-y-1
                  transition-all duration-200 flex flex-col justify-between">

            <div>
                <h2 class="text-lg font-semibold text-blue-700 mb-2">
                    Boletas por Bonos
                </h2>
                <p class="text-sm text-gray-600">
                    Generar boletas para los bonos aplicados a empleados, cargos o todo el personal.
                </p>
            </div>

            <span class="mt-4 text-sm font-medium text-blue-600 group-hover:underline">
                Ingresar
            </span>
        </a>

    </div>

</div>

</body>
</html>
