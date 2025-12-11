<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Empleado;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaCTSController extends Controller
{
    /**
     * Vista principal: tabs + empleados + boletas generadas
     */
    public function index()
    {
        $empleados = Empleado::with(relations: 'contratoActual.cargo')
            ->where('estado', 'activo')
            ->get();

        $boletas = Boleta::with('empleado')
            ->where('tipo', 'cts')
            ->orderBy('periodo_anio', 'desc')
            ->get();

        return view('boletas.cts.index', compact('empleados', 'boletas'));
    }

    /**
     * Validar datos y mostrar resumen previo a generar boleta
     */
    public function confirmar()
    {
        request()->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'periodo'     => 'required|in:mayo,noviembre',
            'anio'        => 'required|integer|min:2000|max:2100',
        ]);

        $empleado = Empleado::with('contratoActual')->findOrFail(request('empleado_id'));
        $periodo  = request('periodo');
        $anio     = request('anio');

        // Rango legal según periodo CTS
        if ($periodo === 'mayo') {
            $inicio = Carbon::create($anio, 11, 1)->subYear(); // 1 nov año anterior
            $fin    = Carbon::create($anio, 4, 30);           // 30 abril año actual
        } else {
            $inicio = Carbon::create($anio, 5, 1);
            $fin    = Carbon::create($anio, 10, 31);
        }

        $ingreso = Carbon::parse($empleado->fecha_ingreso);
        $cese    = $empleado->fecha_cese ? Carbon::parse($empleado->fecha_cese) : null;

        // Ajuste por ingreso tardío
        $inicioPeriodo = $ingreso > $inicio ? $ingreso : $inicio;
        // Ajuste por cese anticipado
        $finPeriodo    = ($cese && $cese < $fin) ? $cese : $fin;

        // Cálculo legal: meses y días
        $mesesCompletos = 0;
        $dias = 0;

        $temp = $inicioPeriodo->copy();

        while ($temp <= $finPeriodo) {
            $inicioMes = $temp->copy()->startOfMonth();
            $finMes    = $temp->copy()->endOfMonth();

            if ($temp->month == $inicioPeriodo->month && $temp->year == $inicioPeriodo->year) {
                // Primer mes
                if ($inicioPeriodo->day == 1) {
                    $mesesCompletos++;
                } else {
                    $dias += $inicioPeriodo->diffInDays($finMes) + 1;
                }
            } elseif ($temp->month == $finPeriodo->month && $temp->year == $finPeriodo->year) {
                // Último mes
                if ($finPeriodo->day == $finMes->day) {
                    $mesesCompletos++;
                } else {
                    $dias += $inicioMes->diffInDays($finPeriodo) + 1;
                }
            } else {
                // Mes entero
                $mesesCompletos++;
            }

            $temp->addMonth();
        }

        // Base CTS
        $sueldo     = $empleado->contratoActual->sueldo ?? 0;
        $asignacion = $empleado->asignacion_familiar ? 102.50 : 0;
        $base       = $sueldo + $asignacion;

        // Cálculo CTS legal
        $montoMeses = ($base / 12) * $mesesCompletos;
        $montoDias  = ($base / 360) * $dias;
        $totalCTS   = round($montoMeses + $montoDias, 2);

        return view('boletas.cts.confirmar', [
            'empleado'        => $empleado,
            'periodo'         => $periodo,
            'anio'            => $anio,
            'inicio'          => $inicioPeriodo,
            'fin'             => $finPeriodo,
            'meses'           => $mesesCompletos,
            'dias'            => $dias,
            'sueldo'          => $sueldo,
            'asignacion'      => $asignacion,
            'base'            => $base,
            'totalCTS'        => $totalCTS,
        ]);
    }

    /**
     * Guarda boleta en base de datos
     */
    public function generar()
    {
        $empleado = Empleado::findOrFail(request('empleado_id'));

    // Mapeo de periodo a código
    $map = [
        'mayo'      => 1,
        'noviembre' => 2,
    ];

    $periodoCodigo = $map[request('periodo')] ?? null;

    // Validar que exista el periodo
    if (!$periodoCodigo) {
        return back()->with('error', 'Periodo inválido.');
    }

    $boleta = Boleta::create([
        'empleado_id'      => $empleado->id,
        'tipo'             => 'cts',
        'periodo_mes'      => $periodoCodigo,  // <--- AHORA ES ENTERO, YA NO TEXTO
        'periodo_anio'     => request('anio'),
        'total_ingresos'   => request('total'),
        'total_descuentos' => 0,
        'total_aportes'    => 0,
        'neto_pagar'       => request('total'),
        'estado'           => 'generado',
        'fecha_generacion' => now(),
    ]);

    return redirect()
        ->route('boletas.cts.index', ['tab' => 'generadas'])
        ->with('success', 'Boleta de CTS generada correctamente.');
    }

    /**
     * PDF completo con los cálculos
     */
    public function pdf($id)
    {
        $boleta = Boleta::with('empleado.contratoActual.cargo')->findOrFail($id);
        $empleado = $boleta->empleado;

        $periodo = $boleta->periodo_mes;
        $anio    = $boleta->periodo_anio;

        // Recalcular CTS (igual que confirmar)
        if ($periodo === 'mayo') {
            $inicio = Carbon::create($anio, 11, 1)->subYear();
            $fin    = Carbon::create($anio, 4, 30);
        } else {
            $inicio = Carbon::create($anio, 5, 1);
            $fin    = Carbon::create($anio, 10, 31);
        }

        $ingreso = Carbon::parse($empleado->fecha_ingreso);
        $cese    = $empleado->fecha_cese ? Carbon::parse($empleado->fecha_cese) : null;

        $inicioPeriodo = $ingreso > $inicio ? $ingreso : $inicio;
        $finPeriodo    = ($cese && $cese < $fin) ? $cese : $fin;

        // Calcular meses y días
        $meses = 0;
        $dias = 0;

        $tmp = $inicioPeriodo->copy();

        while ($tmp <= $finPeriodo) {
            $inicioMes = $tmp->copy()->startOfMonth();
            $finMes    = $tmp->copy()->endOfMonth();

            if ($tmp->month == $inicioPeriodo->month && $tmp->year == $inicioPeriodo->year) {
                if ($inicioPeriodo->day == 1) $meses++;
                else $dias += $inicioPeriodo->diffInDays($finMes) + 1;
            } elseif ($tmp->month == $finPeriodo->month && $tmp->year == $finPeriodo->year) {
                if ($finPeriodo->day == $finMes->day) $meses++;
                else $dias += $inicioMes->diffInDays($finPeriodo) + 1;
            } else {
                $meses++;
            }

            $tmp->addMonth();
        }

        // Base CTS
        $sueldo     = $empleado->contratoActual->sueldo ?? 0;
        $asignacion = $empleado->asignacion_familiar ? 102.50 : 0;
        $base       = $sueldo + $asignacion;

        $total = ($base / 12) * $meses + ($base / 360) * $dias;
        $totalCTS = round($total, 2);

        // Nombre archivo
        $empleadoNombre = strtoupper(str_replace(' ', '_', $empleado->apellidos . '_' . $empleado->nombres));
        $nombreArchivo = "{$empleadoNombre}_boleta_CTS_{$periodo}_{$anio}.pdf";

        return Pdf::loadView('boletas.cts.pdf', [
            'boleta'      => $boleta,
            'empleado'    => $empleado,
            'periodo'     => $periodo,
            'anio'        => $anio,
            'inicio'      => $inicioPeriodo,
            'fin'         => $finPeriodo,
            'meses'       => $meses,
            'dias'        => $dias,
            'sueldo'      => $sueldo,
            'asignacion'  => $asignacion,
            'base'        => $base,
            'totalCTS'    => $totalCTS,
        ])->stream($nombreArchivo);
    }
}
