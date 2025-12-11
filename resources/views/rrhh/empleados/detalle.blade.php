<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { background:#0A4BD6; }
        .card { background:white; border:2px solid #0A4BD6; }
        .title { color:#0A4BD6; font-weight:800; }
        th { color:#0A4BD6; font-weight:700; }
        .btn-white {
            padding: 8px 20px;
            background:white;
            color:#0A4BD6;
            border-radius:8px;
            font-weight:600;
            box-shadow:0 2px 6px rgba(0,0,0,0.15);
        }
        .btn-white:hover { background:#f3f3f3; }
    </style>
</head>

<body class="min-h-screen p-10 text-white">

<div class="max-w-5xl mx-auto card rounded-2xl p-10 shadow-xl text-black">

@php
    $contrato = $empleado->contratoActual;

    // Antigüedad exacta
    $antiguedad = $contrato
        ? \Carbon\Carbon::parse($contrato->fecha_inicio)->diff(\Carbon\Carbon::now())->format('%y años, %m meses, %d días')
        : '—';

    // Fecha ingreso
    $fechaIngreso = $contrato?->fecha_inicio ? \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') : '—';

    // Fecha cese
    $fechaCese = $contrato?->fecha_fin ? \Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : '—';

    // Tiempo para finalizar contrato
    $tiempoRestante = $contrato?->fecha_fin
        ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($contrato->fecha_fin), false) . ' días'
        : 'Indefinido';

    // Última boleta emitida
    $ultimaBoleta = $empleado->boletas->last();
@endphp

<!-- ENCABEZADO -->
<div class="flex justify-between items-center mb-10">
    <div>
        <h1 class="text-3xl title">{{ strtoupper($empleado->nombres . ' ' . $empleado->apellidos) }}</h1>
        <p class="mt-2 text-gray-700 font-medium">
            DNI: {{ $empleado->dni }} |
            Cargo actual: {{ $contrato?->cargo?->cargo ?? '—' }} |
            Área: {{ $contrato?->area?->nombre ?? '—' }}
        </p>
    </div>

    <a href="{{ route('empleados.index') }}" class="btn-white">← Volver</a>
</div>

<!-- DATOS PERSONALES -->
<div class="card p-6 rounded-xl mb-6">
    <h2 class="text-2xl title mb-4">Datos Personales</h2>

    <div class="grid grid-cols-2 gap-6 text-gray-700 font-medium">
        <p><strong>Correo:</strong> {{ $empleado->correo }}</p>
        <p><strong>Fecha nacimiento:</strong> {{ $empleado->fecha_nacimiento?->format('d/m/Y') }}</p>

        <p><strong>Sexo:</strong> {{ $empleado->sexo }}</p>
        <p><strong>Estado Civil:</strong> {{ $empleado->estado_civil }}</p>

        <p><strong>Nacionalidad:</strong> {{ $empleado->nacionalidad }}</p>
        <p><strong>Teléfono:</strong> {{ $empleado->telefono }}</p>

        <p class="col-span-2"><strong>Dirección:</strong> {{ $empleado->direccion }}</p>
    </div>
</div>

<!-- CONTACTO DE EMERGENCIA -->
<div class="card p-6 rounded-xl mb-6">
    <h2 class="text-2xl title mb-4">Contacto de Emergencia</h2>

    <div class="grid grid-cols-2 gap-6 text-gray-700 font-medium">
        <p><strong>Nombre:</strong> {{ $empleado->contacto_nombre }}</p>
        <p><strong>Teléfono:</strong> {{ $empleado->contacto_telefono }}</p>
        <p><strong>Parentesco:</strong> {{ $empleado->contacto_parentesco }}</p>
    </div>
</div>

<!-- SITUACIÓN LABORAL -->
<div class="card p-6 rounded-xl mb-6">
    <h2 class="text-2xl title mb-4">Situación Laboral</h2>

    <div class="grid grid-cols-2 gap-6 text-gray-700 font-medium">

        <!-- Estado -->
        <p><strong>Estado del empleado:</strong> {{ ucfirst($empleado->estado) }}</p>

        <!-- Vacaciones -->
        <p><strong>Vacaciones disponibles:</strong> {{ $vacaciones }} días</p>


        <!-- Fecha ingreso -->
        <p><strong>Fecha ingreso:</strong>
            {{ $ultimoContrato?->fecha_inicio
                ? \Carbon\Carbon::parse($ultimoContrato->fecha_inicio)->format('d/m/Y')
                : '—' }}
        </p>

        <!-- Fecha cese -->
        <p><strong>Fecha cese:</strong>
            {{ $ultimoContrato?->fecha_fin
                ? \Carbon\Carbon::parse($ultimoContrato->fecha_fin)->format('d/m/Y')
                : '—' }}
        </p>


        <!-- Antigüedad -->
        <p><strong>Antigüedad:</strong> {{ $antiguedad }}</p>

        <!-- Tipo contrato -->
        <p><strong>Tipo de contrato:</strong>
            {{ $ultimoContrato?->tipo_contrato ?? '—' }}
        </p>


        <!-- Tiempo restante -->
        <p><strong>Tiempo restante del contrato:</strong>
            {{ $tiempoRestante }}
        </p>

        <!-- Asignación familiar -->
        <p><strong>Asignación familiar:</strong>
            {{ $empleado->asignacion_familiar ? 'Sí (S/ 102.50)' : 'No' }}
        </p>


        <!-- Sistema pensiones -->
        <p><strong>Sistema de pensiones:</strong>
            {{ $ultimoContrato?->sistema_pension ?? '—' }}
        </p>

        <!-- AFP tipo -->
        <p><strong>AFP Tipo:</strong>
            {{ $ultimoContrato?->afp_tipo ?? '—' }}
        </p>


        <!-- Método de pago -->
        <p><strong>Método de pago:</strong>
            {{ $ultimoContrato?->metodo_pago ?? '—' }}
        </p>

        <!-- Cuenta bancaria -->
        <p><strong>Cuenta bancaria:</strong>
            {{ $ultimoContrato?->cuenta_bancaria ?? '—' }}
        </p>

    </div>
</div>

<!-- INFORMACIÓN LABORAL DETALLADA -->
<div class="card p-6 rounded-xl mb-10">
    <h2 class="text-2xl title mb-6">Información Laboral Detallada</h2>

    <!-- TARJETAS RESUMEN -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">

        <!-- Sueldo Actual -->
        <div class="p-4 border-2 border-[#0A4BD6] rounded-xl shadow-sm bg-white">
            <p class="text-sm text-gray-600">Sueldo base actual</p>
            <p class="text-xl font-bold text-blue-700">
                S/ {{ number_format($contrato?->sueldo ?? 0, 2) }}
            </p>
        </div>

        <!-- Última boleta -->
        <div class="p-4 border-2 border-[#0A4BD6] rounded-xl shadow-sm bg-white">
            <p class="text-sm text-gray-600">Última boleta emitida</p>
            <p class="text-xl font-bold text-blue-700">
                {{ $ultimaBoleta?->fecha_generacion?->format('d/m/Y') ?? '—' }}
            </p>
        </div>

        <!-- Total sanciones -->
        <div class="p-4 border-2 border-[#0A4BD6] rounded-xl shadow-sm bg-white">
            <p class="text-sm text-gray-600">Total sanciones</p>
            <p class="text-xl font-bold text-blue-700">
                {{ $empleado->sanciones->count() }}
            </p>
        </div>

        <!-- Última sanción -->
        <div class="p-4 border-2 border-[#0A4BD6] rounded-xl shadow-sm bg-white">
            <p class="text-sm text-gray-600">Última sanción</p>
            <p class="text-xl font-bold text-blue-700 leading-tight">
                {{ $empleado->sanciones->last()?->tipo?->nombre ?? '—' }}
            </p>
        </div>

    </div>

    <!-- TARJETA DE BOLETAS POR TIPO -->
    <div class="p-6 border-2 border-[#0A4BD6] rounded-xl bg-white shadow-sm">
        <h3 class="text-lg font-bold text-blue-700 mb-3">Boletas por tipo</h3>

        <ul class="grid grid-cols-1 md:grid-cols-2 gap-y-2 text-gray-700 font-medium pl-4 list-disc">
            <li>Fin de mes: {{ $empleado->boletas->where('tipo','fin_mes')->count() }}</li>
            <li>CTS: {{ $empleado->boletas->where('tipo','cts')->count() }}</li>
            <li>Liquidaciones: {{ $empleado->boletas->where('tipo','liquidacion')->count() }}</li>
            <li>Gratificaciones: {{ $empleado->boletas->where('tipo','gratificacion')->count() }}</li>
            <li>Bonos: {{ $empleado->boletas->where('tipo','bono')->count() }}</li>
        </ul>
    </div>
</div>


<!-- HISTORIAL DE SANCIONES -->
<div class="card p-6 rounded-xl mt-10">
    <h2 class="text-2xl title mb-4">Historial de Sanciones</h2>

    @if($empleado->sanciones->count() == 0)
        <p class="text-gray-600">No tiene sanciones registradas.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center">
                <thead class="bg-white">
                    <tr class="border-b-2 border-[#0A4BD6]">
                        <th class="py-2">Tipo</th>
                        <th class="py-2">Fecha</th>
                        <th class="py-2">Días</th>
                        <th class="py-2">Descripción</th>
                        <th class="py-2">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($empleado->sanciones as $san)
                        <tr class="border-b border-gray-300 text-gray-700">

                            <!-- Tipo -->
                            <td class="py-2">{{ $san->tipo->nombre ?? '—' }}</td>

                            <!-- Fecha -->
                            <td class="py-2">
                                {{ \Carbon\Carbon::parse($san->fecha_aplicacion)->format('d/m/Y') }}
                            </td>

                            <!-- Días -->
                            <td class="py-2">
                                {{ $san->dias_suspension ? $san->dias_suspension : '—' }}
                            </td>

                            <!-- Motivo -->
                            <td class="py-2">{{ $san->motivo ?? '—' }}</td>

                            <!-- Estado -->
                            <td class="py-2">
                                @php
                                    $estado = 'Aplicada';
                                    if ($san->dias_suspension) {
                                        // Si es suspensión
                                        $estado = $san->estado ?? 'Activo';
                                    }
                                @endphp

                                @if($estado === 'Activo')
                                    <span class="px-3 py-1 rounded-full bg-yellow-500 text-white text-xs font-semibold">
                                        Activo
                                    </span>
                                @elseif($estado === 'En curso')
                                    <span class="px-3 py-1 rounded-full bg-blue-500 text-white text-xs font-semibold">
                                        En curso
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-green-600 text-white text-xs font-semibold">
                                        Aplicada
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    @endif
</div>


<!-- HISTORIAL DE BOLETAS -->
<div class="card p-6 rounded-xl mt-10">
    <h2 class="text-2xl title mb-4">Historial de Boletas</h2>

    <!-- FILTRO -->
    <div class="mb-5">
        <select id="filtroBoleta"
                onchange="filtrarBoletas()"
                class="px-4 py-2 rounded-lg border border-[#0A4BD6] text-gray-700 shadow w-full md:w-1/3">
            <option value="todos">Todas</option>
            <option value="fin_mes">Fin de mes</option>
            <option value="liquidacion">Liquidación</option>
            <option value="cts">CTS</option>
            <option value="gratificacion">Gratificación</option>
            <option value="bono">Bono</option>
        </select>
    </div>

    @if($empleado->boletas->count() == 0)
        <p class="text-gray-600">No tiene boletas generadas.</p>
    @else

        <div class="overflow-x-auto">
            <table id="tablaBoletas" class="w-full text-sm text-center">

                <thead class="bg-white">
                    <tr class="border-b-2 border-[#0A4BD6]">
                        <th class="py-2">Tipo</th>
                        <th class="py-2">Periodo</th>
                        <th class="py-2">Emitida</th>
                        <th class="py-2">Total</th>
                        <th class="py-2">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($empleado->boletas as $bol)

                        @php
                            // FORMATO PERIODO
                            $periodo = $bol->periodo_mes && $bol->periodo_anio
                                ? $bol->periodo_mes . '/' . $bol->periodo_anio
                                : '—';

                            // TOTAL REAL QUE EXISTE
                            $total = $bol->neto_pagar ?? 0;

                            // RUTA PDF SEGÚN TIPO
                            switch($bol->tipo){
                                case 'fin_mes':
                                    $ruta = route('boletas.fin_mes.pdf', $bol->id);
                                break;

                                case 'cts':
                                    $ruta = route('boletas.cts.pdf', $bol->id);
                                break;

                                case 'liquidacion':
                                    $ruta = route('boletas.liquidacion.pdf', $bol->id);
                                break;

                                case 'gratificacion':
                                    $ruta = route('boletas.gratificacion.pdf', $bol->id);
                                break;

                                case 'bono':
                                    $ruta = route('boletas.bonos.pdf', $bol->id);
                                break;

                                default:
                                    $ruta = '#';
                                break;
                            }
                        @endphp

                        <tr class="border-b border-gray-300 text-gray-700"
                            data-tipo="{{ strtolower($bol->tipo) }}">

                            <td class="py-2 text-blue-700 font-semibold">
                                {{ ucfirst($bol->tipo) }}
                            </td>

                            <td class="py-2">{{ $periodo }}</td>

                            <td class="py-2">
                                {{ $bol->fecha_generacion?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="py-2 font-bold">
                                S/ {{ number_format($total, 2) }}
                            </td>

                            <td class="py-2">
                                <a href="{{ $ruta }}"
                                   class="px-4 py-1 bg-pink-600 hover:bg-pink-700 
                                          text-white rounded-lg text-xs">
                                    Ver boleta
                                </a>
                            </td>
                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

    @endif
</div>


<script>
function filtrarBoletas() {
    let tipo = document.getElementById('filtroBoleta').value;
    let filas = document.querySelectorAll('#tablaBoletas tbody tr');

    filas.forEach(fila => {
        if (tipo === 'todos') {
            fila.style.display = '';
        } else {
            fila.style.display = 
                (fila.dataset.tipo === tipo.toLowerCase()) ? '' : 'none';
        }
    });
}
</script>


<!-- TARJETA DE ASISTENCIAS (CON FILTRO POR MES Y AÑO) -->
<div id="asistencias" class="card p-6 rounded-xl mt-10 border-2 border-[#0A4BD6]">

    <h2 class="text-2xl title mb-6">Resumen de Asistencias</h2>

    <!-- SELECTS DE FILTROS -->
    <div class="mb-6 flex items-center gap-6">

        <!-- FILTRO MES -->
        <div>
            <label class="text-gray-700 font-semibold">Mes:</label>

                @php
                    \Carbon\Carbon::setLocale('es');
                @endphp
            <select id="filtroMes"
                    onchange="actualizarFiltros()"
                    class="px-4 py-2 ml-2 rounded-lg border border-[#0A4BD6] text-gray-700 shadow">
                
                <option value="actual" {{ $mes == 'actual' ? 'selected' : '' }}>Mes actual</option>
                <option value="anterior" {{ $mes == 'anterior' ? 'selected' : '' }}>Mes anterior</option>
                <option value="todos" {{ $mes == 'todos' ? 'selected' : '' }}>Todo el año</option>

                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $i, 1)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- FILTRO AÑO -->
        <div>
            <label class="text-gray-700 font-semibold">Año:</label>
            <select id="filtroAnio"
                    onchange="actualizarFiltros()"
                    class="px-4 py-2 ml-2 rounded-lg border border-[#0A4BD6] text-gray-700 shadow">

                @for($a = 2024; $a <= 2027; $a++)
                    <option value="{{ $a }}" {{ (isset($anio) && $anio == $a) ? 'selected' : '' }}>
                        {{ $a }}
                    </option>
                @endfor
            </select>
        </div>

    </div>

    @php
        use Carbon\Carbon;

        // ---------------------------
        // DEFINIR AÑO
        // ---------------------------
        $anioFiltro = $anio ?? Carbon::now()->year;

        // ---------------------------
        // DEFINIR MES
        // ---------------------------
        $hoy = Carbon::now();

        if ($mes === 'actual') {
            $mesNum = $hoy->month;
        } elseif ($mes === 'anterior') {
            $mesNum = $hoy->copy()->subMonth()->month;
        } elseif ($mes === 'todos') {
            $mesNum = null;
        } else {
            $mesNum = intval($mes);
        }

        // ---------------------------
        // FILTRAR ASISTENCIAS
        // ---------------------------
        $asistencias = $empleado->asistencias->filter(function ($a) use ($mesNum, $anioFiltro) {
            $f = Carbon::parse($a->fecha);

            if ($mesNum) {
                return $f->month == $mesNum && $f->year == $anioFiltro;
            }

            return $f->year == $anioFiltro;
        });

        // Cálculos
        $totalAsistencias = $asistencias->count();
        $faltas           = $asistencias->where('estado_asistencia', 'falta')->count();
        $descansos        = $asistencias->where('estado_asistencia', 'descanso')->count();
        $tardanzaTotal    = $asistencias->sum('tardanza_total');
        $suspensionesDias = $empleado->sanciones->sum('dias_suspension');

        $puntuales = $asistencias->where('tardanza_total', 0)->count();
        $puntualidad = $totalAsistencias > 0 ? round(($puntuales / $totalAsistencias) * 100, 1) : 0;

        $ultimaAsistencia = $asistencias->last()?->fecha
            ? Carbon::parse($asistencias->last()->fecha)->format('d/m/Y')
            : '—';
    @endphp

    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-4 gap-6">

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Asistencias</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalAsistencias }}</p>
        </div>

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Faltas</p>
            <p class="text-2xl font-bold text-red-600">{{ $faltas }}</p>
        </div>

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Descansos</p>
            <p class="text-2xl font-bold text-green-600">{{ $descansos }}</p>
        </div>

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Suspensiones (días)</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $suspensionesDias }}</p>
        </div>

    </div>

    <!-- SEGUNDA FILA -->
    <div class="grid grid-cols-3 gap-6 mt-6">

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Tardanza total</p>
            <p class="text-xl font-bold text-orange-600">{{ $tardanzaTotal }} min</p>
        </div>

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Puntualidad</p>
            <p class="text-xl font-bold text-blue-700">{{ $puntualidad }}%</p>
        </div>

        <div class="p-4 bg-white border border-[#0A4BD6] rounded-lg shadow-sm">
            <p class="font-semibold text-gray-700">Última asistencia</p>
            <p class="text-xl font-bold text-gray-800">{{ $ultimaAsistencia }}</p>
        </div>

    </div>

</div>

<script>
function actualizarFiltros(){

    // Tomar valores
    const mes  = document.getElementById("filtroMes").value;
    const anio = document.getElementById("filtroAnio").value;

    const url = new URL(window.location.href);
    url.searchParams.set("mes", mes);
    url.searchParams.set("anio", anio);

    // Guardar bandera en localStorage para hacer scroll después
    localStorage.setItem("volverAsistencias", "1");

    // recargar
    window.location.href = url.toString();
}

// --- Cuando cargue la página ---
document.addEventListener("DOMContentLoaded", function(){
    if(localStorage.getItem("volverAsistencias") === "1"){
        const target = document.getElementById("asistencias");

        if(target){
            target.scrollIntoView({ behavior: "smooth", block: "start" });
        }

        // limpiar bandera
        localStorage.removeItem("volverAsistencias");
    }
});
</script>




    </div>

</body>
</html>
