<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Asistencia</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #0A4BD6;
            min-height: 100vh;
        }
        .card {
            background: #ffffff;
            border: 2px solid #0A4BD6;
        }
        th {
            background: #0A4BD6;
            color: white;
            text-align: center;
        }
        .estado-turno { background:#15803d; color:white; }
        .estado-falta { background:#dc2626; color:white; }
        .estado-descanso { background:#4b5563; color:white; }
        .estado-otros { background:#1e40af; color:white; }

        /* Tamaños uniformes para todas las celdas horarias */
        .celda-horario input,
        .celda-horario select {
            width: 100%;
        }
    </style>

    <script>
        /* --------------------------
           CONTROL DE ESTADOS
        --------------------------- */
        function onEstadoChange(sel, id) {
            const estado = sel.value;

            const turno = document.getElementById("turno-" + id);
            const celdas = document.querySelectorAll(".celdas-" + id);
            const info = document.getElementById("info-" + id);
            const obs = document.getElementById("obs-" + id);

            // SI ES ESTADO "TURNO" O "FERIADO TRABAJADO"
            if (estado === "turno" || estado === "feriado_trabajado") {

                // Mostrar turno + inputs horario
                turno.classList.remove("hidden");

                celdas.forEach(c => {
                    c.style.display = "table-cell";
                });

                // Ocultar bloque combinado
                info.style.display = "none";
                info.textContent = "";
                info.colSpan = 1;

                // Obs siempre editable
                obs.removeAttribute("disabled");

                return;
            }

            // SI ES FALTA, DESCANSO, LICENCIAS, ETC → COMBINAR CELDAS
            turno.classList.add("hidden");
            turno.value = "";

            celdas.forEach(c => {
                c.style.display = "none";
                const inp = c.querySelector("input");
                if (inp) inp.value = "";
            });

            // COMBINAR DESDE TURNO HASTA T.BREAK (7 columnas)
            info.style.display = "table-cell";
            info.textContent = sel.options[sel.selectedIndex].text;
            info.colSpan = 7;

            // Obs habilitado
            obs.removeAttribute("disabled");
        }


        /* --------------------------
           MODALES
        --------------------------- */

        function abrirModalEditar(id, nombre, estado, turnoId, hi, hs, bi, bf, obs) {
            document.getElementById("m-empleado").textContent = nombre;
            document.getElementById("m-estado").value = estado || "";
            document.getElementById("m-turno_id").value = turnoId || "";
            document.getElementById("m-hora_entrada").value = hi || "";
            document.getElementById("m-hora_salida").value = hs || "";
            document.getElementById("m-break_inicio").value = bi || "";
            document.getElementById("m-break_fin").value = bf || "";
            document.getElementById("m-observaciones").value = obs || "";

            document.getElementById("form-editar").action =
                "{{ route('asistencias.update','__ID__') }}".replace("__ID__", id);

            document.getElementById("modal-editar").classList.remove("hidden");
        }

        function cerrarModalEditar() {
            document.getElementById("modal-editar").classList.add("hidden");
        }

        function abrirModalEliminar(id, nombre) {
            document.getElementById("del-texto").textContent =
                "¿Eliminar asistencia de " + nombre + "?";

            document.getElementById("form-eliminar").action =
                "{{ route('asistencias.destroy','__ID__') }}".replace("__ID__", id);

            document.getElementById("modal-eliminar").classList.remove("hidden");
        }

        function cerrarModalEliminar() {
            document.getElementById("modal-eliminar").classList.add("hidden");
        }

        /* --------------- ANIMAR MENSAJE ---------------- */
        document.addEventListener("DOMContentLoaded", () => {
            const alerta = document.getElementById("alert-success");
            if (alerta) {
                setTimeout(() => {
                    alerta.classList.add("opacity-0");
                    setTimeout(() => alerta.remove(), 500);
                }, 2800);
            }
        });
    </script>
</head>

<body class="p-6">
<div class="card rounded-xl p-6 mx-auto max-w-[1800px]">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-[#0A4BD6]">Registrar Asistencia</h1>

        <a href="{{ route('admin.dashboard') }}"
           class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-4 py-2 rounded-lg transition">
            Regresar
        </a>
    </div>

    <!-- SELECTOR DE FECHA -->
    <form action="{{ route('asistencias.registrar') }}" method="GET" class="mb-4">
        <label class="text-[#0A4BD6] font-semibold">Fecha:</label>
        <div class="flex gap-3 mt-1">
            <input type="date" name="fecha" value="{{ $fecha }}"
                   class="border border-[#0A4BD6] rounded px-3 py-2">
            <button class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-4 py-2 rounded-lg transition">
                Cambiar
            </button>
        </div>
    </form>

    <!-- MENSAJES -->
    @if(session('success'))
        <div id="alert-success"
             class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- FORM PRINCIPAL -->
    <form method="POST" action="{{ route('asistencias.store') }}">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha }}">

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm bg-white">
                <thead>
                <tr>
                    <th class="border px-2 py-2">DNI</th>
                    <th class="border px-2 py-2">Empleado</th>
                    <th class="border px-2 py-2">Cargo</th>
                    <th class="border px-2 py-2">Estado</th>
                    <th class="border px-2 py-2">Turno</th>
                    <th class="border px-2 py-2">Entrada</th>
                    <th class="border px-2 py-2">Salida</th>
                    <th class="border px-2 py-2">Break In</th>
                    <th class="border px-2 py-2">Break Out</th>
                    <th class="border px-2 py-2 w-20">T. Inicio</th>
                    <th class="border px-2 py-2 w-20">T. Break</th>
                    <th class="border px-2 py-2">Obs</th>
                    <th class="border px-2 py-2">Acciones</th>
                </tr>
                </thead>

                <tbody>

                @php use Carbon\Carbon; @endphp

                @foreach($empleados as $e)

                    @php
                        $asis  = $e->asistencias->first();
                        $cargo = optional($e->cargoActual())->cargo ?? '---';
                    @endphp

                    {{-- ASISTENCIA YA REGISTRADA --}}
                    @if($asis)
                        <tr>
                            <td class="border px-2 py-2 text-center">{{ $e->dni }}</td>
                            <td class="border px-2 py-2">{{ $e->apellidos }} {{ $e->nombres }}</td>
                            <td class="border px-2 py-2">{{ $cargo }}</td>

                            <td class="border px-2 py-2 text-center">
                                <span class="
                                    @if($asis->estado_asistencia=='turno') estado-turno
                                    @elseif($asis->estado_asistencia=='falta') estado-falta
                                    @elseif($asis->estado_asistencia=='descanso') estado-descanso
                                    @else estado-otros @endif
                                    px-2 py-1 rounded">
                                    {{ ucfirst(str_replace('_',' ',$asis->estado_asistencia)) }}
                                </span>
                            </td>

                            <td class="border px-2 py-2 text-center">
                                {{ $asis->turno?->nombre ?? '---' }}
                            </td>

                            <td class="border px-2 py-2 text-center">{{ $asis->hora_entrada ?? '---' }}</td>
                            <td class="border px-2 py-2 text-center">{{ $asis->hora_salida ?? '---' }}</td>
                            <td class="border px-2 py-2 text-center">{{ $asis->break_inicio ?? '---' }}</td>
                            <td class="border px-2 py-2 text-center">{{ $asis->break_fin ?? '---' }}</td>

                            <td class="border px-2 py-2 text-center">
                                {{ $asis->tardanza_inicio_turno ? $asis->tardanza_inicio_turno.' min' : '0 min' }}
                            </td>

                            <td class="border px-2 py-2 text-center">
                                {{ $asis->tardanza_break ? $asis->tardanza_break.' min' : '0 min' }}
                            </td>

                            <td class="border px-2 py-2 text-center">{{ $asis->observaciones }}</td>

                            <td class="border px-2 py-2 text-center">
                                <button type="button"
                                        onclick="abrirModalEditar(
                                            '{{ $asis->id }}',
                                            '{{ $e->apellidos }} {{ $e->nombres }}',
                                            '{{ $asis->estado_asistencia }}',
                                            '{{ $asis->turno_id }}',
                                            '{{ $asis->hora_entrada }}',
                                            '{{ $asis->hora_salida }}',
                                            '{{ $asis->break_inicio }}',
                                            '{{ $asis->break_fin }}',
                                            '{{ addslashes($asis->observaciones ?? '') }}'
                                        )"
                                        class="px-3 py-1 bg-yellow-500 text-white rounded">
                                    Editar
                                </button>

                                <button type="button"
                                        onclick="abrirModalEliminar('{{ $asis->id }}','{{ $e->apellidos }} {{ $e->nombres }}')"
                                        class="px-3 py-1 bg-red-600 text-white rounded">
                                    Eliminar
                                </button>
                            </td>
                        </tr>

                    {{-- SIN ASISTENCIA — NUEVO REGISTRO --}}
                    @else

                        @php
                            $contrato = $e->contratoActual;
                            $inicio   = $contrato?->fecha_inicio ? Carbon::parse($contrato->fecha_inicio) : null;
                            $fin      = $contrato?->fecha_fin ? Carbon::parse($contrato->fecha_fin) : null;
                            $fAsis    = Carbon::parse($fecha);

                            $fueraRango = false;

                            if ($inicio && $fAsis->lt($inicio)) {
                                $fueraRango = "El empleado ingresó el " . $inicio->format('d/m/Y') . ". No puede registrar asistencia antes de esa fecha.";
                            }

                            if ($fin && $fAsis->gt($fin)) {
                                $fueraRango = "El contrato del empleado terminó el " . $fin->format('d/m/Y') . ". No puede registrar asistencia después de esa fecha.";
                            }
                        @endphp

                        @if($fueraRango)
                            {{-- MENSAJE CUANDO LA FECHA ESTÁ FUERA DEL RANGO DEL CONTRATO --}}
                            <tr class="bg-red-50">
                                <td class="border px-2 py-2 text-center">{{ $e->dni }}</td>
                                <td class="border px-2 py-2">{{ $e->apellidos }} {{ $e->nombres }}</td>
                                <td class="border px-2 py-2">{{ $cargo }}</td>

                                {{-- CELDA COMBINADA --}}
                                <td class="border px-4 py-2 text-center font-semibold text-red-700 bg-red-100"
                                    colspan="10">
                                    {{ $fueraRango }}
                                </td>

                                <td class="border px-2 py-2 text-center">—</td>
                            </tr>
                        @else
                            {{-- BLOQUE ORIGINAL PARA NUEVA ASISTENCIA --}}
                            <tr>
                                <td class="border px-2 py-2 text-center">{{ $e->dni }}</td>
                                <td class="border px-2 py-2">{{ $e->apellidos }} {{ $e->nombres }}</td>
                                <td class="border px-2 py-2">{{ $cargo }}</td>

                                <!-- ESTADO -->
                                <td class="border px-2 py-2">
                                    <select name="asistencias[{{ $e->id }}][estado_asistencia]"
                                            onchange="onEstadoChange(this, {{ $e->id }})"
                                            class="border rounded w-full px-2 py-1">
                                        <option value="">-Seleccione-</option>
                                        <option value="turno">Turno</option>
                                        <option value="falta">Falta</option>
                                        <option value="descanso">Descanso</option>
                                        <option value="descanso_medico">Descanso médico</option>
                                        <option value="feriado_trabajado">Feriado trabajado</option>
                                        <option value="feriado_no_trabajado">Feriado NO trabajado</option>
                                        <option value="licencia_sin_goce">Licencia sin goce</option>
                                        <option value="licencia_con_goce">Licencia con goce</option>
                                        <option value="suspension">Suspensión</option>
                                    </select>
                                </td>

                                <!-- TURNO -->
                                <td class="border px-2 py-2 text-center celda-horario">
                                    <select id="turno-{{ $e->id }}"
                                            name="asistencias[{{ $e->id }}][turno_id]"
                                            class="border rounded w-full px-1 py-1 hidden">
                                        <option value="">-Turno-</option>
                                        @foreach($turnos as $t)
                                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- HORARIOS -->
                                @foreach(['hora_entrada','hora_salida','break_inicio','break_fin'] as $campo)
                                    <td class="border px-2 py-2 celda-horario celdas-{{ $e->id }}" style="display:none;">
                                        <input type="time" name="asistencias[{{ $e->id }}][{{ $campo }}]"
                                               class="border rounded px-2 py-1">
                                    </td>
                                @endforeach

                                <!-- T. INICIO -->
                                <td class="border px-2 py-2 text-center celda-horario celdas-{{ $e->id }}"
                                    style="display:none;">
                                    <span>0 min</span>
                                </td>

                                <!-- T. BREAK -->
                                <td class="border px-2 py-2 text-center celda-horario celdas-{{ $e->id }}"
                                    style="display:none;">
                                    <span>0 min</span>
                                </td>

                                <!-- CELDA COMBINADA -->
                                <td id="info-{{ $e->id }}"
                                    class="border px-2 py-2 text-center bg-gray-100 text-gray-600 font-semibold"
                                    style="display:none;"
                                    colspan="7"></td>

                                <!-- OBS -->
                                <td class="border px-2 py-2 text-center">
                                    <input type="text"
                                           id="obs-{{ $e->id }}"
                                           name="asistencias[{{ $e->id }}][observaciones]"
                                           class="border rounded px-2 py-1 w-48">
                                </td>

                                <td class="border px-2 py-2 text-center">—</td>
                            </tr>
                        @endif

                    @endif {{-- fin if $asis --}}
                @endforeach

                </tbody>
            </table>
        </div>

        <div class="mt-4 text-right">
            <button class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-6 py-2 rounded-lg transition">
                Registrar Asistencias
            </button>
        </div>

    </form>
</div>


<!-- MODAL EDITAR -->
<div id="modal-editar"
     class="fixed inset-0 bg-black/40 hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg border-2 border-[#0A4BD6] rounded-xl p-4">

        <h2 class="text-xl font-bold text-[#0A4BD6] mb-3">Editar Asistencia</h2>

        <form id="form-editar" method="POST">
            @csrf
            @method('PUT')

            <p class="mb-3 font-semibold">
                Empleado: <span id="m-empleado" class="text-[#0A4BD6]"></span>
            </p>

            <div class="grid grid-cols-2 gap-3">

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Estado</label>
                    <select id="m-estado" name="estado_asistencia"
                            class="border rounded w-full px-2 py-1">
                        <option value="turno">Turno</option>
                        <option value="falta">Falta</option>
                        <option value="descanso">Descanso</option>
                        <option value="descanso_medico">Descanso médico</option>
                        <option value="feriado_trabajado">Feriado trabajado</option>
                        <option value="feriado_no_trabajado">Feriado NO trabajado</option>
                        <option value="licencia_con_goce">Licencia con goce</option>
                        <option value="licencia_sin_goce">Licencia sin goce</option>
                        <option value="suspension">Suspensión</option>
                    </select>
                </div>

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Turno</label>
                    <select id="m-turno_id" name="turno_id"
                            class="border rounded w-full px-2 py-1">
                        <option value="">-Turno-</option>
                        @foreach($turnos as $t)
                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Entrada</label>
                    <input id="m-hora_entrada" type="time" name="hora_entrada"
                           class="border rounded w-full px-2 py-1">
                </div>

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Salida</label>
                    <input id="m-hora_salida" type="time" name="hora_salida"
                           class="border rounded w-full px-2 py-1">
                </div>

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Break Inicio</label>
                    <input id="m-break_inicio" type="time" name="break_inicio"
                           class="border rounded w-full px-2 py-1">
                </div>

                <div>
                    <label class="font-semibold text-sm text-[#0A4BD6]">Break Fin</label>
                    <input id="m-break_fin" type="time" name="break_fin"
                           class="border rounded w-full px-2 py-1">
                </div>

                <div class="col-span-2">
                    <label class="font-semibold text-sm text-[#0A4BD6]">Observaciones</label>
                    <textarea id="m-observaciones" name="observaciones" rows="2"
                              class="border rounded w-full px-2 py-1"></textarea>
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="cerrarModalEditar()"
                        class="border px-4 py-1 rounded">Cancelar</button>

                <button type="submit"
                        class="bg-[#0A4BD6] hover:bg-[#083CA8] text-white px-5 py-1 rounded transition">
                    Guardar
                </button>
            </div>

        </form>

    </div>
</div>


<!-- MODAL ELIMINAR -->
<div id="modal-eliminar"
     class="fixed inset-0 bg-black/40 hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md border-2 border-red-700 rounded-xl p-4">

        <h2 class="text-xl font-bold text-red-700 mb-3">Eliminar Asistencia</h2>

        <p id="del-texto" class="mb-4"></p>

        <form id="form-eliminar" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="cerrarModalEliminar()"
                        class="border px-4 py-1 rounded">
                    Cancelar
                </button>

                <button type="submit"
                        class="bg-red-700 hover:bg-red-800 text-white px-5 py-1 rounded transition">
                    Eliminar
                </button>

            </div>
        </form>

    </div>
</div>

</body>
</html>
