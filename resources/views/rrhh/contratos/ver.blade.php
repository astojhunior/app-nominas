<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato del Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { background:#0A4BD6; }
        .card { background:white; border:2px solid #0A4BD6; }
        .title { color:#0A4BD6; font-weight:800; }
        .btn-blue {
            @apply px-5 py-2 bg-[#0A4BD6] text-white rounded-xl shadow hover:bg-blue-800 transition;
        }
        .btn-outline {
            @apply px-5 py-2 border-2 border-white text-white rounded-xl hover:bg-white hover:text-[#0A4BD6];
        }
    </style>
</head>

<body class="min-h-screen p-10 text-white">

    <div class="max-w-5xl mx-auto">

        <!-- BOTONES SUPERIORES -->
        <div class="flex justify-between items-center mb-8">
            
            <!-- VOLVER -->
            <a href="{{ route('empleados.index') }}"
               class="btn-outline">
               ← Volver
            </a>

            <!-- DESCARGAR PDF -->
            <a href="{{ route('contratos.descargarPDF', $contrato->id) }}"
               class="bg-white text-[#0A4BD6] px-5 py-2 font-semibold rounded-xl shadow hover:bg-gray-200 transition">
                Descargar PDF
            </a>
        </div>

        <!-- TARJETA PRINCIPAL -->
        <div class="card text-black p-8 rounded-3xl shadow-xl">

            <h1 class="text-3xl title mb-6">
                Contrato de {{ strtoupper($contrato->empleado->nombres . ' ' . $contrato->empleado->apellidos) }}
            </h1>

            <!-- DOS COLUMNAS DE INFORMACIÓN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <div class="bg-gray-100 p-5 rounded-xl border border-gray-300">
                    <p><strong>Tipo:</strong> {{ $contrato->tipo_contrato }}</p>
                    <p><strong>Inicio:</strong> {{ $contrato->fecha_inicio ? \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') : '—' }}</p>
                    <p><strong>Fin:</strong> {{ $contrato->fecha_fin ? \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : '—' }}</p>
                </div>

                <div class="bg-gray-100 p-5 rounded-xl border border-gray-300">
                    <p><strong>Área:</strong> {{ $contrato->area?->nombre }}</p>
                    <p><strong>Cargo:</strong> {{ $contrato->cargo?->cargo }}</p>
                    <p><strong>Sueldo:</strong> S/ {{ number_format($contrato->sueldo, 2) }}</p>
                </div>

            </div>

            <!-- SUBIR CONTRATO FIRMADO -->
            <div class="bg-gray-100 p-5 rounded-xl border border-gray-300 mb-8">

                <h2 class="text-xl font-semibold mb-4 text-[#0A4BD6]">Subir contrato firmado</h2>

                @if (session('success'))
                    <div class="mb-3 px-4 py-2 rounded bg-green-600 text-white text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-3 px-4 py-2 rounded bg-red-600 text-white text-sm">
                        {{ $errors->first('archivo') }}
                    </div>
                @endif

                <form action="{{ route('contratos.subirFirmado', $contrato->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="flex flex-col md:flex-row items-center gap-4">

                        <input type="file" name="archivo"
                               class="px-4 py-2 rounded-lg border border-gray-400 text-sm bg-white text-black">

                        <button class="btn-blue">
                            Subir archivo
                        </button>

                    </div>

                    <p class="text-xs text-gray-600 mt-2">Formato PDF, máximo 5 MB.</p>
                </form>
            </div>

            <!-- ARCHIVOS SUBIDOS -->
            <div class="bg-gray-100 p-5 rounded-xl border border-gray-300">

                <h2 class="text-xl font-semibold mb-4 text-[#0A4BD6]">Archivos subidos</h2>

                @if ($contrato->archivos->count() == 0)
                    <p class="text-gray-600">Aún no hay archivos adjuntos.</p>
                @else
                    <ul class="list-disc ml-6 text-sm">
                        @foreach ($contrato->archivos as $archivo)
                            <li class="mb-2">
                                <a href="{{ asset('storage/'.$archivo->ruta_archivo) }}"
                                   target="_blank"
                                   class="text-[#0A4BD6] font-semibold hover:underline">
                                    {{ $archivo->tipo_archivo }} – Ver archivo
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>

        </div>

    </div>

</body>
</html>
