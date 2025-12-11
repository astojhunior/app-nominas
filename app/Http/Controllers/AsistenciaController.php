<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Cargo;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciasExport;

class AsistenciaController extends Controller
{
public function index(Request $request)
{
    // Traemos TODAS las asistencias con su empleado y cargo actual
    $asistencias = Asistencia::with([
        'empleado',
        'empleado.contratoActual.cargo'
    ])
    ->orderBy('fecha', 'desc')
    ->get();

    // Lista de cargos para el combo
    $cargos = \App\Models\Cargo::all();

    return view('asistencias.ListaAsistencias', compact(
        'asistencias',
        'cargos'
    ));
}



    /**
     * Mostrar pantalla Registrar Asistencia
     */
    public function create(Request $request)
    {
        $fecha = $request->input('fecha', now()->toDateString());
        $fechaCarbon = Carbon::parse($fecha);

        // Empleados activos + contrato ACTIVO real
        $empleados = Empleado::where('estado', 'activo')
            ->whereHas('contratos', function ($q) {
                $q->where('estado_contrato', 'activo');
            })
            ->with([
                'contratos' => function ($q) {
                    $q->where('estado_contrato', 'activo')
                      ->orderBy('fecha_inicio', 'desc');
                },
                'contratoActual.cargo',   // cargo real
                'contratoActual.turno',   // turno real
                'asistencias' => function ($q) use ($fecha) {
                    $q->whereDate('fecha', $fecha);
                }
            ])
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        // Turnos disponibles
        $turnos = Turno::orderBy('nombre')->get();

        return view('asistencias.RegistrarAsistencia', compact('empleados', 'fecha', 'fechaCarbon', 'turnos'));

    }

/**
 * Registrar asistencias
 */
public function store(Request $request)
{
    $fechaSeleccionada = $request->input('fecha');
    $datosAsistencias  = $request->input('asistencias', []);

    if (!$fechaSeleccionada) {
        return back()->with('error', 'Debe seleccionar una fecha.');
    }

    if (empty($datosAsistencias)) {
        return back()->with('error', 'No hay datos para registrar.');
    }

    $guardados = 0;
    $errores   = [];

    foreach ($datosAsistencias as $empleadoId => $datos) {

        $estado = $datos['estado_asistencia'] ?? null;
        if (!$estado) continue;

        // normalizo por si llega con mayúsculas o espacios
        $estado = strtolower(trim($estado));

        $empleado = Empleado::with(['contratoActual'])
            ->find($empleadoId);

        if (!$empleado || !$empleado->contratoActual) continue;

        // Evitar duplicados
        if (Asistencia::where('empleado_id', $empleadoId)
            ->whereDate('fecha', $fechaSeleccionada)
            ->exists()) continue;

        $turnoId       = $datos['turno_id']      ?? null;
        $horaEntrada   = $datos['hora_entrada']  ?? null;
        $horaSalida    = $datos['hora_salida']   ?? null;
        $breakInicio   = $datos['break_inicio']  ?? null;
        $breakFin      = $datos['break_fin']     ?? null;
        $observaciones = $datos['observaciones'] ?? null;

        // Validación para estados que requieren horas
        if (in_array($estado, ['turno', 'feriado_trabajado'])) {
            if (!$turnoId || !$horaEntrada || !$horaSalida || !$breakInicio || !$breakFin) {
                $errores[] =
                    "El empleado {$empleado->apellidos} {$empleado->nombres} debe completar turno y horas.";
                continue;
            }
        }

        // Cálculo de tardanzas
        $tardanzaInicio = 0;
        $tardanzaBreak  = 0;

        // ---- TARDANZA DE INICIO DE TURNO (igual que tenías) ----
        if ($estado === 'turno' && $turnoId && $horaEntrada) {
            $turno = Turno::find($turnoId);

            if ($turno) {
                $hEsperada = Carbon::parse("$fechaSeleccionada {$turno->hora_ingreso}");
                $hReal     = Carbon::parse("$fechaSeleccionada $horaEntrada");

                if ($hReal->gt($hEsperada)) {
                    $tardanzaInicio = $hEsperada->diffInMinutes($hReal);
                }
            }
        }

        // ---- TARDANZA DE BREAK (ACTUALIZADO) ----
        if ($breakInicio && $breakFin) {

            // Normalizo a HH:MM:SS por si viene HH:MM desde el input time
            $bi = (strlen($breakInicio) === 5) ? $breakInicio . ':00' : $breakInicio;
            $bf = (strlen($breakFin) === 5)     ? $breakFin . ':00'   : $breakFin;

            try {
                $ini = Carbon::createFromFormat('Y-m-d H:i:s', "$fechaSeleccionada $bi");
                $fin = Carbon::createFromFormat('Y-m-d H:i:s', "$fechaSeleccionada $bf");

                $min = $ini->diffInMinutes($fin); // siempre positivo (fin > ini)

                // más de 45 min de refrigerio → tardanza
                if ($min > 45) {
                    $tardanzaBreak = $min - 45;
                }
            } catch (\Exception $e) {
                // si algo raro pasa con el parseo, no rompas el registro
                $tardanzaBreak = 0;
            }
        }

        $tardanzaTotal = $tardanzaInicio + $tardanzaBreak;

        try {
            Asistencia::create([
                'empleado_id'           => $empleadoId,
                'contrato_id'           => $empleado->contratoActual->id,
                'turno_id'              => $turnoId,
                'fecha'                 => $fechaSeleccionada,
                'hora_entrada'          => $horaEntrada,
                'hora_salida'           => $horaSalida,
                'break_inicio'          => $breakInicio,
                'break_fin'             => $breakFin,
                'tardanza_break'        => $tardanzaBreak,
                'tardanza_inicio_turno' => $tardanzaInicio,
                'tardanza_total'        => $tardanzaTotal,
                'tipo_jornada'          => 'completa',
                'estado_asistencia'     => $estado,
                'horas_extra'           => 0,
                'justificacion'         => null,
                'marcado_manual'        => 1,
                'origen_marcado'        => 'manual',
                'observaciones'         => $observaciones,
            ]);

            $guardados++;

        } catch (\Throwable $e) {
            $errores[] =
                "Error al guardar asistencia de {$empleado->apellidos} {$empleado->nombres}: {$e->getMessage()}";
        }
    }

    if (!empty($errores)) {
        return back()
            ->with('success', $guardados ? "$guardados asistencias registradas." : null)
            ->with('error', implode(' | ', $errores));
    }

    if ($guardados > 0) {
        return back()->with('success', "$guardados asistencias registradas correctamente.");
    }

    return back()->with('error', 'No se registró ninguna asistencia válida.');
}



    /**
     * Actualizar asistencia (modal)
     */
    public function update(Request $request, $id)
    {
        $asistencia = Asistencia::findOrFail($id);

        $request->validate([
            'estado_asistencia' => 'required',
        ]);

        $estado        = $request->input('estado_asistencia');
        $turnoId       = $request->input('turno_id');
        $horaEntrada   = $request->input('hora_entrada');
        $horaSalida    = $request->input('hora_salida');
        $breakInicio   = $request->input('break_inicio');
        $breakFin      = $request->input('break_fin');
        $observaciones = $request->input('observaciones');
        $fecha         = $asistencia->fecha;

        // Recalcular tardanzas
        $tardanzaInicio = 0;
        $tardanzaBreak  = 0;

        if ($estado === 'turno' && $turnoId && $horaEntrada) {
    $turno = Turno::find($turnoId);

    if ($turno) {
        $hEsperada = Carbon::parse("$fecha {$turno->hora_ingreso}");
        $hReal     = Carbon::parse("$fecha $horaEntrada");

        if ($hReal->gt($hEsperada)) {
            $tardanzaInicio = $hEsperada->diffInMinutes($hReal);
        }
    }
}

        if ($breakInicio && $breakFin) {
            $ini = Carbon::parse("$fecha $breakInicio");
            $fin = Carbon::parse("$fecha $breakFin");
            $min = $fin->diffInMinutes($ini);

            if ($min > 45) $tardanzaBreak = $min - 45;
        }

        $tTotal = $tardanzaInicio + $tardanzaBreak;

        $asistencia->update([
            'estado_asistencia'     => $estado,
            'turno_id'              => $turnoId ?: null,
            'hora_entrada'          => $horaEntrada ?: null,
            'hora_salida'           => $horaSalida ?: null,
            'break_inicio'          => $breakInicio ?: null,
            'break_fin'             => $breakFin ?: null,
            'tardanza_inicio_turno' => $tardanzaInicio,
            'tardanza_break'        => $tardanzaBreak,
            'tardanza_total'        => $tTotal,
            'observaciones'         => $observaciones,
        ]);

        return back()->with('success', 'Asistencia actualizada correctamente.');
    }

    /**
     * Eliminar asistencia
     */
    public function destroy($id)
    {
        $asistencia = Asistencia::findOrFail($id);
        $asistencia->delete();

        return back()->with('success', 'Asistencia eliminada correctamente.');
    }


public function exportExcel(Request $request)
{
    $buscar = strtolower($request->buscar);
    $cargo  = strtolower($request->cargo);
    $estado = strtolower($request->estado);
    $desde  = $request->desde;
    $hasta  = $request->hasta;

    $asistencias = Asistencia::with(['empleado', 'empleado.contratoActual.cargo'])
        ->where(function($q) use ($buscar) {

            if (!$buscar) {
                $q->whereHas('empleado', function($e){
                    $e->where('estado', 'activo');
                });
            }

            if ($buscar) {
                $q->whereHas('empleado', function($e) use ($buscar){
                    $e->whereRaw("LOWER(dni) LIKE ?", ["%{$buscar}%"])
                      ->orWhereRaw("LOWER(CONCAT(apellidos,' ',nombres)) LIKE ?", ["%{$buscar}%"]);
                });
            }

        })
        ->when($cargo, function($q) use ($cargo){
            $q->whereHas('empleado.contratoActual.cargo', function($c) use ($cargo){
                $c->whereRaw("LOWER(cargo) = ?", [$cargo]);
            });
        })
        ->when($estado, function($q) use ($estado){
            $q->whereRaw("LOWER(estado_asistencia) = ?", [$estado]);
        })
        ->when($desde, function($q) use ($desde){
            $q->where('fecha', '>=', $desde);
        })
        ->when($hasta, function($q) use ($hasta){
            $q->where('fecha', '<=', $hasta);
        })
        ->orderBy('fecha', 'desc')
        ->get();

    return Excel::download(new AsistenciasExport($asistencias), 'asistencias.xlsx');
}


public function exportPdf(Request $request)
{
    $buscar = strtolower($request->buscar);
    $cargo  = strtolower($request->cargo);
    $estado = strtolower($request->estado);
    $desde  = $request->desde;
    $hasta  = $request->hasta;

    $asistencias = Asistencia::with(['empleado', 'empleado.contratoActual.cargo'])
        ->where(function($q) use ($buscar) {

            if (!$buscar) {
                $q->whereHas('empleado', function($e){
                    $e->where('estado', 'activo');
                });
            }

            if ($buscar) {
                $q->whereHas('empleado', function($e) use ($buscar){
                    $e->whereRaw("LOWER(dni) LIKE ?", ["%{$buscar}%"])
                      ->orWhereRaw("LOWER(CONCAT(apellidos,' ',nombres)) LIKE ?", ["%{$buscar}%"]);
                });
            }

        })
        ->when($cargo, function($q) use ($cargo){
            $q->whereHas('empleado.contratoActual.cargo', function($c) use ($cargo){
                $c->whereRaw("LOWER(cargo) = ?", [$cargo]);
            });
        })
        ->when($estado, function($q) use ($estado){
            $q->whereRaw("LOWER(estado_asistencia) = ?", [$estado]);
        })
        ->when($desde, function($q) use ($desde){
            $q->where('fecha', '>=', $desde);
        })
        ->when($hasta, function($q) use ($hasta){
            $q->where('fecha', '<=', $hasta);
        })
        ->orderBy('fecha', 'desc')
        ->get();

   $pdf = Pdf::loadView('asistencias.pdf', compact('asistencias'))
          ->setPaper('a4', 'landscape');
    return $pdf->download('asistencias.pdf');
}

}

