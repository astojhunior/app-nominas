<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar generación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-600 to-blue-800 min-h-screen text-white p-6">

<div class="max-w-5xl mx-auto bg-white text-gray-900 rounded-2xl p-8 shadow-lg">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-blue-700">Confirmar generación de boletas</h1>

        <a href="{{ route('boletas.bonos.index') }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- Info Bono -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <h2 class="text-xl font-bold text-blue-700">{{ $bono->nombre }}</h2>
        <p class="text-gray-700">Monto: <strong>S/ {{ number_format($bono->monto, 2) }}</strong></p>
        <p class="text-gray-700">Fecha aplicación: {{ $bono->fecha_aplicacion }}</p>
        <p class="text-gray-700">Dirigido a: <strong>{{ ucfirst($bono->dirigido_a) }}</strong></p>
    </div>

    <!-- Lista de empleados -->
    <h3 class="text-2xl font-bold text-blue-700 mb-3">Empleados que recibirán el bono</h3>

    <ul class="space-y-3">
        @foreach($empleados as $empleado)
            <li class="bg-white border border-blue-200 p-4 rounded-xl shadow-sm">
                <span class="font-semibold text-blue-700">
                    {{ $empleado->nombres }} {{ $empleado->apellidos }}
                </span>

                <span class="block text-gray-600 text-sm">
                    DNI: {{ $empleado->dni }}
                </span>
            </li>
        @endforeach
    </ul>

    <!-- Botón Confirmar -->
    <form method="POST" action="{{ route('boletas.bonos.generar', $bono->id) }}" class="mt-8">
        @csrf

        <button class="w-full py-3 rounded-lg bg-gray-900 text-white font-semibold hover:bg-black transition">
            Generar Boletas de Bono
        </button>
    </form>

</div>

</body>
</html>
