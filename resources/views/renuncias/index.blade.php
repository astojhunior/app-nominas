<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Renuncias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-8"
     x-data="renunciasApp">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Gestión de Renuncias
        </h1>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- PESTAÑAS -->
    @php
        $tab = request()->query('ver', 'registrar');
    @endphp

    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <a href="{{ route('renuncias.index',['ver'=>'registrar']) }}"
               class="pb-3 transition-all duration-200
               {{ $tab=='registrar'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600' }}">
                Registrar Renuncia
            </a>

            <a href="{{ route('renuncias.index',['ver'=>'lista']) }}"
               class="pb-3 transition-all duration-200
               {{ $tab=='lista'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600' }}">
                Lista de Renuncias
            </a>

            <a href="{{ route('renuncias.index',['ver'=>'mes']) }}"
               class="pb-3 transition-all duration-200
               {{ $tab=='mes'
                    ? 'text-blue-700 border-b-4 border-blue-700'
                    : 'text-gray-500 hover:text-blue-600' }}">
                Renuncias por Mes
            </a>
        </div>
    </div>

    <!-- MENSAJES -->
    @if(session('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 shadow-lg animate-fade">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4 shadow-lg animate-fade">
            {{ session('error') }}
        </div>
    @endif

    <!-- TAB 1 — REGISTRAR -->
    @if($tab=='registrar')

        <form action="{{ route('renuncias.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="font-semibold">Empleado</label>
                <select name="empleado_id"
                        class="w-full border rounded-lg px-3 py-2 mt-1" required>
                    <option value="">Seleccione</option>
                    @foreach($empleados as $e)
                        <option value="{{ $e->id }}">
                            {{ $e->apellidos }} {{ $e->nombres }} ({{ $e->dni }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-semibold">Fecha de Renuncia</label>
                <input type="date" name="fecha_renuncia"
                       class="w-full border rounded-lg px-3 py-2 mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Fecha de Cese (último día)</label>
                <input type="date" name="fecha_cese"
                       class="w-full border rounded-lg px-3 py-2 mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Motivo</label>
                <textarea name="motivo" rows="2"
                          class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
            </div>

            <button
                class="bg-blue-700 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                Guardar Renuncia
            </button>
        </form>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                Corrige los siguientes errores:
                <ul class="mt-2 ml-4 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <!-- TAB 2 — LISTA -->
    @elseif($tab=='lista')

        <div class="mb-6 flex gap-4 items-end">

            <div>
                <label class="font-semibold text-gray-700">Buscar</label>
                <input type="text"
                       x-model="buscar"
                       placeholder="DNI o nombre"
                       class="border rounded-lg px-3 py-2 w-52">
            </div>

            <div>
                <label class="font-semibold text-gray-700">Cargo</label>
                <select x-model="filtroCargo"
                        class="border rounded-lg px-3 py-2 w-40">
                    <option value="">Todos</option>

                    @foreach($empleados->groupBy(fn($e)=>
                        $e->contratoActual && $e->contratoActual->cargo
                            ? $e->contratoActual->cargo->cargo
                            : null
                    ) as $cargo => $items)
                        @if($cargo)
                            <option value="{{ $cargo }}">{{ $cargo }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

        </div>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Fecha Renuncia</th>
                <th class="py-3">Fecha Cese</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @foreach($renuncias as $r)
                <tr class="border-b"
                    x-show="filtrar(
                        '{{ strtolower($r->empleado->dni . ' ' . $r->empleado->apellidos . ' ' . $r->empleado->nombres) }}',
                        '{{ $r->empleado->contratoActual?->cargo?->cargo }}'
                    )">
                    <td class="py-3">{{ $r->empleado->dni }}</td>
                    <td>{{ $r->empleado->apellidos }} {{ $r->empleado->nombres }}</td>
                    <td>{{ $r->empleado->contratoActual?->cargo?->cargo ?? '---' }}</td>
                    <td>{{ $r->fecha_renuncia }}</td>
                    <td>{{ $r->fecha_cese }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    <!-- TAB 3 — POR MES -->
    @elseif($tab=='mes')

        <form method="GET" action="{{ route('renuncias.index') }}" class="mb-6">
            <input type="hidden" name="ver" value="mes">

            <label class="font-semibold">Seleccionar mes</label>
            <select name="m" class="border rounded-lg px-3 py-2 mt-1" onchange="this.form.submit()">
                <option value="">-- Seleccione --</option>

                @foreach($meses as $m)
                    @php
                        $nombreMes = \Carbon\Carbon::create()->month($m->mes)->translatedFormat('F');
                    @endphp
                    <option value="{{ $m->anio.'-'.$m->mes }}"
                        {{ request('m') == $m->anio.'-'.$m->mes ? 'selected' : '' }}>
                        {{ ucfirst($nombreMes) }} {{ $m->anio }}
                    </option>
                @endforeach
            </select>
        </form>

        @if(request('m'))

            @php
                [$anio,$mes] = explode('-', request('m'));
                $listado = $renuncias->filter(fn($r) =>
                    \Carbon\Carbon::parse($r->fecha_cese)->year == $anio &&
                    \Carbon\Carbon::parse($r->fecha_cese)->month == $mes
                );
            @endphp

            <table class="w-full text-center border rounded-lg overflow-hidden">
                <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="py-3">DNI</th>
                    <th class="py-3">Empleado</th>
                    <th class="py-3">Cargo</th>
                    <th class="py-3">Fecha Cese</th>
                    <th class="py-3">PDF</th>
                </tr>
                </thead>

                <tbody class="text-gray-700">
                @forelse($listado as $r)
                    <tr class="border-b">
                        <td class="py-3">{{ $r->empleado->dni }}</td>
                        <td>{{ $r->empleado->apellidos }} {{ $r->empleado->nombres }}</td>
                        <td>{{ $r->empleado->contratoActual?->cargo?->cargo ?? '---' }}</td>
                        <td>{{ $r->fecha_cese }}</td>
                        <td>
                            <a href="{{ route('renuncias.pdf', $r->id) }}"
                               class="text-blue-600 hover:underline"
                               target="_blank">
                                Descargar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-gray-500">
                            No hay renuncias en este mes.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        @endif
    @endif

</div>

<style>
    .animate-fade {
        animation: fade 0.5s ease-in-out;
    }

    @keyframes fade {
        from { opacity: 0; transform: translateY(-5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('renunciasApp', () => ({
            buscar: '',
            filtroCargo: '',

            filtrar(texto, cargo) {
                texto = texto.toLowerCase();

                return (
                    texto.includes(this.buscar.toLowerCase()) &&
                    (this.filtroCargo === '' || this.filtroCargo === cargo)
                );
            }
        }))
    })
</script>

</body>
</html>
