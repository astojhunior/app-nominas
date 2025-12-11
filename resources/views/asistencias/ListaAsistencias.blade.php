<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Asistencias</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { background: #0A4BD6; min-height: 100vh; }
        .card { background: #ffffff; border: 2px solid #0A4BD6; }
        th { background: #0A4BD6; color: white; text-align: center; }

        .estado-turno    { background: #22c55e; color: white; font-weight: bold; }
        .estado-descanso { background: #9ca3af; color: white; font-weight: bold; }
        .estado-falta    { background: #ef4444; color: white; font-weight: bold; }
        .estado-licencia { background: #38bdf8; color: white; font-weight: bold; }
        .estado-permiso  { background: #f97316; color: white; font-weight: bold; }
    </style>

    <script>
        // ======= SINCRONIZAR FILTROS PARA EXCEL / PDF =======
        function syncFilters() {
            const filtros = ['buscar','cargo','estado','fecha_desde','fecha_hasta'];

            filtros.forEach(f => {
                const origen = document.getElementById(f);
                document.getElementById('exp_' + f).value = origen.value;
                document.getElementById('pdf_' + f).value = origen.value;
            });
        }

        // ======= FILTRO EN TABLA (INCLUIDO ACTIVO / INACTIVO) =======
        function filtrarTabla() {
            let texto      = document.getElementById('buscar').value.toLowerCase();
            let cargo      = document.getElementById('cargo').value.toLowerCase();
            let estado     = document.getElementById('estado').value.toLowerCase();
            let fechaDesde = document.getElementById('fecha_desde').value;
            let fechaHasta = document.getElementById('fecha_hasta').value;

            document.querySelectorAll('#tabla-asist tbody tr').forEach(fila => {
                let dni            = fila.dataset.dni;
                let nombre         = fila.dataset.nombre;
                let cargoFila      = fila.dataset.cargo;
                let estadoFila     = fila.dataset.estado;
                let fechaFila      = fila.dataset.fecha;
                let estadoEmpleado = fila.dataset.estadoEmpleado; // activo / inactivo

                // Coincidencias básicas
                let coincideTexto =
                    dni.includes(texto) || nombre.includes(texto);

                let coincideCargo =
                    cargo === "" || cargoFila === cargo;

                let coincideEstado =
                    estado === "" || estadoFila === estado;

                let coincideFechaDesde =
                    fechaDesde === "" || fechaFila >= fechaDesde;

                let coincideFechaHasta =
                    fechaHasta === "" || fechaFila <= fechaHasta;

                let coincideFiltros =
                    coincideCargo && coincideEstado &&
                    coincideFechaDesde && coincideFechaHasta;

                let mostrar = false;

                if (texto === '') {
                    // SIN BUSCADOR → SOLO EMPLEADOS ACTIVOS
                    mostrar = (estadoEmpleado === 'activo') && coincideFiltros;
                } else {
                    // CON BUSCADOR → PUEDE MOSTRAR INACTIVOS
                    mostrar = coincideTexto && coincideFiltros;
                }

                fila.style.display = mostrar ? "" : "none";
            });
        }

        // Aplicar regla "solo activos" al cargar
        window.addEventListener('DOMContentLoaded', filtrarTabla);
    </script>
</head>

<body class="p-6">

<div class="card rounded-xl p-6 mx-auto max-w-[1800px]">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#0A4BD6]">Lista de Asistencias</h1>

        <div class="flex gap-3">

            <!-- EXPORT EXCEL -->
            <form action="{{ route('asistencias.export.excel') }}"
                  method="GET" onsubmit="syncFilters()">
                <input type="hidden" id="exp_buscar"      name="buscar">
                <input type="hidden" id="exp_cargo"       name="cargo">
                <input type="hidden" id="exp_estado"      name="estado">
                <input type="hidden" id="exp_fecha_desde" name="desde">
                <input type="hidden" id="exp_fecha_hasta" name="hasta">

                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
                    Exportar Excel
                </button>
            </form>

            <!-- EXPORT PDF -->
            <form action="{{ route('asistencias.export.pdf') }}"
                  method="GET" onsubmit="syncFilters()">
                <input type="hidden" id="pdf_buscar"      name="buscar">
                <input type="hidden" id="pdf_cargo"       name="cargo">
                <input type="hidden" id="pdf_estado"      name="estado">
                <input type="hidden" id="pdf_fecha_desde" name="desde">
                <input type="hidden" id="pdf_fecha_hasta" name="hasta">

                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold shadow">
                    Exportar PDF
                </button>
            </form>

            <!-- REGRESAR -->
            <a href="{{ route('admin.dashboard') }}"
               class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-4 py-2 rounded-lg transition">
                Regresar
            </a>
        </div>
    </div>

    <!-- BUSQUEDA Y FILTROS -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">

        <!-- Buscar -->
        <input id="buscar" name="buscar" onkeyup="filtrarTabla()"
               placeholder="Buscar por DNI o nombre..."
               class="border border-[#0A4BD6] rounded px-3 py-2">

        <!-- Cargo -->
        <select id="cargo" name="cargo" onchange="filtrarTabla()"
                class="border border-[#0A4BD6] rounded px-3 py-2">
            <option value="">Todos los cargos</option>
            @foreach($cargos as $c)
                <option value="{{ strtolower($c->cargo) }}">{{ $c->cargo }}</option>
            @endforeach
        </select>

        <!-- Estado -->
        <select id="estado" name="estado" onchange="filtrarTabla()"
                class="border border-[#0A4BD6] rounded px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="turno">Turno</option>
            <option value="descanso">Descanso</option>
            <option value="falta">Falta</option>
            <option value="licencia">Licencia</option>
            <option value="permiso">Permiso</option>
        </select>

        <!-- Fecha Desde -->
        <input type="date" id="fecha_desde" name="fecha_desde"
               onchange="filtrarTabla()"
               class="border border-[#0A4BD6] rounded px-3 py-2">

        <!-- Fecha Hasta -->
        <input type="date" id="fecha_hasta" name="fecha_hasta"
               onchange="filtrarTabla()"
               class="border border-[#0A4BD6] rounded px-3 py-2">
    </div>

    <!-- TABLA -->
    <div class="overflow-x-auto">
        <table id="tabla-asist" class="w-full text-sm border border-gray-300 bg-white">
            <thead>
                <tr>
                    <th class="p-2 border">DNI</th>
                    <th class="p-2 border">Empleado</th>
                    <th class="p-2 border">Cargo</th>
                    <th class="p-2 border">Fecha</th>
                    <th class="p-2 border">Estado</th>
                    <th class="p-2 border">Entrada</th>
                    <th class="p-2 border">Salida</th>
                    <th class="p-2 border">Break</th>
                    <th class="p-2 border">T. Inicio</th>
                    <th class="p-2 border">T. Break</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Observaciones</th>
                </tr>
            </thead>

            <tbody>
            @forelse($asistencias as $a)
                @php
                    $estadoAsis = strtolower($a->estado_asistencia);
                    $claseEstado =
                        $estadoAsis === 'turno'    ? 'estado-turno'    :
                        ($estadoAsis === 'descanso'? 'estado-descanso' :
                        ($estadoAsis === 'falta'   ? 'estado-falta'    :
                        ($estadoAsis === 'licencia'? 'estado-licencia' :
                                                     'estado-permiso')));
                @endphp

                <tr class="hover:bg-gray-50"
                    data-dni="{{ strtolower($a->empleado->dni) }}"
                    data-nombre="{{ strtolower($a->empleado->apellidos.' '.$a->empleado->nombres) }}"
                    data-cargo="{{ strtolower(optional($a->empleado->cargoActual())->cargo) }}"
                    data-estado="{{ strtolower($a->estado_asistencia) }}"
                    data-fecha="{{ $a->fecha }}"
                    data-estado-empleado="{{ strtolower($a->empleado->estado) }}">

                    <td class="border px-2 py-2 text-center">{{ $a->empleado->dni }}</td>

                    <td class="border px-2 py-2 font-semibold">
                        {{ $a->empleado->apellidos }} {{ $a->empleado->nombres }}
                    </td>

                    <td class="border px-2 py-2 text-center">
                        {{ optional($a->empleado->cargoActual())->cargo ?? '---' }}
                    </td>

                    <td class="border px-2 py-2 text-center">{{ $a->fecha }}</td>

                    <td class="border px-2 py-2 text-center {{ $claseEstado }}">
                        {{ ucfirst($a->estado_asistencia) }}
                    </td>

                    @if($estadoAsis === 'descanso' || $estadoAsis === 'falta')
                        <td class="border px-2 py-2 text-center italic text-gray-500" colspan="6">
                            {{ ucfirst($estadoAsis) }} — No requiere registro de jornada
                        </td>
                    @else
                        <td class="border px-2 py-2 text-center">{{ $a->hora_entrada ?? '---' }}</td>
                        <td class="border px-2 py-2 text-center">{{ $a->hora_salida ?? '---' }}</td>
                        <td class="border px-2 py-2 text-center">
                            {{ $a->break_inicio ?? '---' }} - {{ $a->break_fin ?? '---' }}
                        </td>
                        <td class="border px-2 py-2 text-center">{{ $a->tardanza_inicio_turno ?? '0' }} min</td>
                        <td class="border px-2 py-2 text-center">{{ $a->tardanza_break ?? '0' }} min</td>
                        <td class="border px-2 py-2 text-center font-bold text-blue-700">
                            {{ $a->tardanza_total ?? '0' }} min
                        </td>
                        <td class="border px-2 py-2 text-center">{{ $a->observaciones ?? '—' }}</td>
                    @endif

                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-4 bg-gray-100 text-gray-600 font-semibold">
                        No existen asistencias registradas.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
