<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Sanciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        function toggleDias() {
            const select = document.getElementById('tipo_sancion_id');
            const requiere = select.selectedOptions[0]?.dataset.requiere == "1";

            const dias = document.getElementById('dias_suspension');
            const fin = document.getElementById('fecha_fin_suspension');

            dias.disabled = !requiere;
            fin.disabled = !requiere;

            if (!requiere) {
                dias.value = "";
                fin.value = "";
            }
        }

        function calcularFin() {
            const inicio = document.getElementById('fecha_aplicacion').value;
            const dias = document.getElementById('dias_suspension').value;

            if (!inicio || !dias) return;

            const fecha = new Date(inicio);
            fecha.setDate(fecha.getDate() + parseInt(dias) - 1);

            document.getElementById('fecha_fin_suspension').value =
                fecha.toISOString().split('T')[0];
        }
    </script>
</head>

<body class="bg-[#1859D1] min-h-screen flex items-center justify-center p-6">

<div class="bg-white w-full max-w-6xl rounded-2xl shadow-xl p-8">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-blue-700">
            Gestión de Sanciones
        </h1>

        <a href="{{ route('admin.dashboard') }}"
           class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-black transition">
            Volver
        </a>
    </div>

    <!-- PESTAÑAS -->
    <div class="border-b border-gray-300 mb-6">
        <div class="flex gap-6 text-lg font-semibold">

            <a href="{{ route('sanciones.index') }}"
               class="pb-3 {{ !request('ver') ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                Empleados Activos
            </a>

            <a href="{{ route('sanciones.index', ['ver' => 'registrar']) }}"
               class="pb-3 {{ request('ver')=='registrar' ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                Registrar Sanción
            </a>

            <a href="{{ route('sanciones.index', ['ver' => 'lista']) }}"
               class="pb-3 {{ request('ver')=='lista' ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                Lista de Sanciones
            </a>

            <a href="{{ route('sanciones.index', ['ver' => 'pdf']) }}"
               class="pb-3 {{ request('ver')=='pdf' ? 'text-blue-700 border-b-4 border-blue-700' : 'text-gray-500 hover:text-blue-600' }}">
                PDFs por Mes
            </a>
        </div>
    </div>

    <!-- TAB 1 — EMPLEADOS ACTIVOS -->
    @if(!request('ver'))

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @foreach($empleados as $e)
                <tr class="border-b">
                    <td class="py-3">{{ $e->dni }}</td>
                    <td>{{ $e->apellidos }} {{ $e->nombres }}</td>
                    <td>{{ $e->contratoActual?->cargo?->cargo ?? '---' }}</td>

                    <td class="py-3">
                        <a href="{{ route('sanciones.index', ['ver'=>'registrar', 'empleado'=>$e->id]) }}"
                           class="text-blue-600 font-semibold hover:underline">
                            Registrar Sanción
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    <!-- TAB 2 — REGISTRAR -->
    @elseif(request('ver')=='registrar')

        @php $emp = $empleadoSeleccionado ?? null; @endphp

        <form action="{{ route('sanciones.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="font-bold text-blue-700 mb-2">Empleado seleccionado</h3>

                @if($emp)
                    <p><b>DNI:</b> {{ $emp->dni }}</p>
                    <p><b>Nombre:</b> {{ $emp->apellidos }} {{ $emp->nombres }}</p>
                    <p><b>Cargo:</b> {{ $emp->contratoActual?->cargo?->cargo ?? '---' }}</p>
                    <input type="hidden" name="empleado_id" value="{{ $emp->id }}">
                @else
                    <p class="text-gray-500">Seleccione un empleado desde la pestaña inicial.</p>
                @endif
            </div>

            <div>
                <label class="font-semibold">Tipo de sanción</label>
                <select name="tipo_sancion_id" id="tipo_sancion_id"
                        class="w-full border rounded-lg px-3 py-2 mt-1"
                        onchange="toggleDias()" required>
                    <option value="">Seleccione</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id }}" data-requiere="{{ $t->requiere_dias }}">
                            {{ $t->nombre }} {{ $t->requiere_dias ? '(con días)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-semibold">Fecha inicio</label>
                <input type="date" name="fecha_aplicacion" id="fecha_aplicacion"
                       onchange="calcularFin()"
                       class="w-full border rounded-lg px-3 py-2 mt-1" required>
            </div>

            <div>
                <label class="font-semibold">Días suspensión (si aplica)</label>
                <input type="number" name="dias_suspension" id="dias_suspension"
                       oninput="calcularFin()"
                       class="w-full border rounded-lg px-3 py-2 mt-1" min="1" disabled>
            </div>

            <div>
                <label class="font-semibold">Fecha fin (auto)</label>
                <input type="date" name="fecha_fin_suspension" id="fecha_fin_suspension"
                       class="w-full border rounded-lg px-3 py-2 mt-1" readonly disabled>
            </div>

            <div>
                <label class="font-semibold">Observaciones</label>
                <textarea name="observaciones" rows="2"
                          class="w-full border rounded-lg px-3 py-2 mt-1"></textarea>
            </div>

            <button class="bg-blue-700 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-800 transition">
                Guardar y Generar PDF
            </button>
        </form>

    <!--  TAB 3 — LISTA DE SANCIONES -->
    @elseif(request('ver')=='lista')

        <!-- FILTROS -->
        <form method="GET" action="{{ route('sanciones.index') }}" class="mb-6 flex gap-4 items-end">
            <input type="hidden" name="ver" value="lista">

            <div>
                <label class="font-semibold text-gray-700">Buscar</label>
                <input type="text" name="buscar"
                       value="{{ request('buscar') }}"
                       placeholder="DNI o nombre"
                       class="border rounded-lg px-3 py-2 w-52">
            </div>

            <div>
                <label class="font-semibold text-gray-700">Tipo</label>
                <select name="tipo" class="border rounded-lg px-3 py-2 w-40">
                    <option value="">Todos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id }}" {{ request('tipo')==$t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Cargo</label>
                <select name="cargo" class="border rounded-lg px-3 py-2 w-40">
                    <option value="">Todos</option>
                    @foreach($empleados->groupBy(fn($e)=>$e->contratoActual?->cargo?->cargo) as $cargo => $items)
                        @if($cargo)
                            <option value="{{ $cargo }}" {{ request('cargo')==$cargo ? 'selected' : '' }}>
                                {{ $cargo }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-semibold text-gray-700">Orden</label>
                <select name="orden" class="border rounded-lg px-3 py-2 w-32">
                    <option value="">Fecha</option>
                    <option value="az" {{ request('orden')=='az' ? 'selected' : '' }}>A-Z</option>
                    <option value="za" {{ request('orden')=='za' ? 'selected' : '' }}>Z-A</option>
                </select>
            </div>

            <button class="px-4 py-2 bg-blue-700 text-white rounded-lg shadow">
                Aplicar
            </button>
        </form>

        <table class="w-full text-center border rounded-lg overflow-hidden">
            <thead class="bg-blue-700 text-white">
            <tr>
                <th class="py-3">DNI</th>
                <th class="py-3">Empleado</th>
                <th class="py-3">Cargo</th>
                <th class="py-3">Tipo</th>
                <th class="py-3">Duración</th>
                <th class="py-3">Motivo</th>
                <th class="py-3">Acción</th>
            </tr>
            </thead>

            <tbody class="text-gray-700">
            @forelse($sanciones as $s)

                @continue($s->estado === 'anulado')

                <tr class="border-b">

                    <td class="py-3">{{ $s->empleado->dni }}</td>
                    <td>{{ $s->empleado->apellidos }} {{ $s->empleado->nombres }}</td>
                    <td>{{ $s->empleado->contratoActual?->cargo?->cargo ?? '---' }}</td>
                    <td>{{ $s->tipo->nombre }}</td>

                    <td>
                        @if($s->fecha_fin_suspension)
                            {{ $s->fecha_aplicacion }} → {{ $s->fecha_fin_suspension }}
                        @else
                            ---
                        @endif
                    </td>

                    <td>{{ $s->motivo ?? '---' }}</td>

                    <td class="py-3 flex justify-center gap-4">

                        <button
                            x-data
                            @click="$dispatch('open-edit-{{ $s->id }}')"
                            class="text-blue-600 font-semibold hover:underline">
                            Editar
                        </button>

                        <button
                            x-data
                            @click="$dispatch('open-anular-{{ $s->id }}')"
                            class="text-red-600 font-semibold hover:underline">
                            Anular
                        </button>

                        <a href="{{ route('sanciones.pdf', $s->id) }}"
                           class="text-gray-600 font-semibold hover:underline"
                           target="_blank">
                            PDF
                        </a>

                    </td>
                </tr>

                <!-- MODAL EDITAR -->
                <div
                    x-data="{ open: false }"
                    @open-edit-{{ $s->id }}.window="open = true"
                    x-show="open"
                    style="display:none"
                    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">

                    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

                        <h2 class="text-xl font-bold text-blue-700 mb-4">
                            Editar Sanción
                        </h2>

                       <form action="{{ route('sanciones.update', $s->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                            <div class="mb-3">
                                <label class="font-semibold">Fecha aplicación</label>
                                <input type="date" name="fecha_aplicacion"
                                       value="{{ $s->fecha_aplicacion }}"
                                       class="w-full border rounded-lg px-3 py-2 mt-1">
                            </div>

                            <div class="mb-3">
                                <label class="font-semibold">Días suspensión</label>
                                <input type="number" name="dias_suspension"
                                       value="{{ $s->dias_suspension }}"
                                       class="w-full border rounded-lg px-3 py-2 mt-1" min="1">
                            </div>

                            <div class="mb-3">
                                <label class="font-semibold">Observaciones</label>
                                <textarea name="observaciones" rows="2"
                                          class="w-full border rounded-lg px-3 py-2 mt-1">{{ $s->motivo }}</textarea>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button"
                                        @click="open=false"
                                        class="px-4 py-2 bg-gray-300 rounded-lg">
                                    Cancelar
                                </button>

                                <button class="px-4 py-2 bg-blue-700 text-white rounded-lg">
                                    Guardar cambios
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!--  MODAL ANULAR -->
                <div
                    x-data="{ open: false }"
                    @open-anular-{{ $s->id }}.window="open = true"
                    x-show="open"
                    style="display:none"
                    class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">

                    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

                        <h2 class="text-xl font-bold text-red-600 mb-4">
                            Anular Sanción
                        </h2>

                       <form action="{{ route('sanciones.anular', $s->id) }}" method="POST">
                        @csrf
                        @method('POST')

                            <p class="mb-3 text-gray-700">
                                Ingrese su contraseña para confirmar la anulación.
                            </p>

                            <input type="password" name="password"
                                   class="w-full border rounded-lg px-3 py-2 mb-4"
                                   placeholder="Contraseña" required>

                            <div class="flex justify-end gap-3 mt-2">
                                <button type="button"
                                        @click="open=false"
                                        class="px-4 py-2 bg-gray-300 rounded-lg">
                                    Cancelar
                                </button>

                                <button class="px-4 py-2 bg-red-600 text-white rounded-lg">
                                    Anular
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            @empty
                <tr>
                    <td colspan="7" class="py-4 text-gray-500">
                        No hay sanciones registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    <!--  TAB 4 — PDFs -->
    @elseif(request('ver')=='pdf')

        <form method="GET" action="{{ route('sanciones.index') }}" class="mb-6">
            <input type="hidden" name="ver" value="pdf">

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
                $listado = $sanciones->filter(fn($s) =>
                    \Carbon\Carbon::parse($s->fecha_aplicacion)->year == $anio &&
                    \Carbon\Carbon::parse($s->fecha_aplicacion)->month == $mes
                );
            @endphp

            <table class="w-full text-center border rounded-lg overflow-hidden">
                <thead class="bg-blue-700 text-white">
                <tr>
                    <th class="py-3">DNI</th>
                    <th class="py-3">Empleado</th>
                    <th class="py-3">Cargo</th>
                    <th class="py-3">Tipo</th>
                    <th class="py-3">Motivo</th>
                    <th class="py-3">PDF</th>
                </tr>
                </thead>

                <tbody class="text-gray-700">
                @forelse($listado as $s)
                    <tr class="border-b">
                        <td class="py-3">{{ $s->empleado->dni }}</td>
                        <td>{{ $s->empleado->apellidos }} {{ $s->empleado->nombres }}</td>
                        <td>{{ $s->empleado->contratoActual?->cargo?->cargo ?? '---' }}</td>
                        <td>{{ $s->tipo->nombre }}</td>
                        <td>{{ $s->motivo ?? '---' }}</td>
                        <td class="py-3">
                            <a href="{{ route('sanciones.pdf', $s->id) }}"
                               class="text-blue-600 font-semibold hover:underline"
                               target="_blank">
                                Descargar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-gray-500">
                            No hay sanciones en este mes.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @endif

    @endif

</div>

</body>
</html>
