<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\DetalleBoleta;
use App\Models\Empleado;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BoletaGratificacionController extends Controller
{
    /**
     * Vista principal de gratificación:
     * - Tab 1: seleccionar empleado
     * - Tab 2: seleccionar periodo (Julio / Diciembre)
     * - Tab 3: boletas generadas
     */
    public function index()
    {
        $empleados = Empleado::with('contratoActual.cargo')
            ->where('estado', 'activo')
            ->get();

        $boletas = Boleta::with('empleado')
            ->where('tipo', 'gratificacion')
            ->orderBy('periodo_anio', 'desc')
            ->orderBy('periodo_mes', 'desc')
            ->get();

        return view('boletas.gratificacion.index', compact('empleados', 'boletas'));
    }

    /**
     * Paso 1: mostrar resumen de la gratificación antes de generar
     * (GET: /boletas/gratificacion/confirmar?empleado_id=&periodo_mes=&periodo_anio=)
     */
    public function confirmar()
    {
        request()->validate([
            'empleado_id'  => 'required|exists:empleados,id',
            'periodo_mes'  => 'required|in:7,12', // Julio o Diciembre
            'periodo_anio' => 'required|integer|min:2000|max:2100',
        ]);

        $empleado    = Empleado::with('contratoActual.cargo')->findOrFail(request('empleado_id'));
        $mesGrati    = (int) request('periodo_mes');
        $anio        = (int) request('periodo_anio');

        // Cálculo legal de gratificación
        $calculo = $this->calcularGratificacion($empleado, $mesGrati, $anio);

        $mesNombre = $mesGrati === 7 ? 'Julio' : 'Diciembre';

        return view('boletas.gratificacion.confirmar', [
            'empleado'         => $empleado,
            'mesGrati'         => $mesGrati,
            'anio'             => $anio,
            'mesNombre'        => $mesNombre,
            'calculo'          => $calculo,
        ]);
    }

    /**
     * Paso 2: guardar boleta de gratificación y sus detalles
     */
    public function generar()
    {
        request()->validate([
            'empleado_id'  => 'required|exists:empleados,id',
            'periodo_mes'  => 'required|in:7,12',
            'periodo_anio' => 'required|integer|min:2000|max:2100',
        ]);

        $empleado   = Empleado::with('contratoActual')->findOrFail(request('empleado_id'));
        $mesGrati   = (int) request('periodo_mes');
        $anio       = (int) request('periodo_anio');

        // Evitar duplicados
        $existe = Boleta::where('empleado_id', $empleado->id)
            ->where('tipo', 'gratificacion')
            ->where('periodo_mes', $mesGrati)
            ->where('periodo_anio', $anio)
            ->exists();

        if ($existe) {
            return redirect()
                ->route('boletas.gratificacion.index', ['tab' => 'generadas'])
                ->with('error', 'Ya existe una boleta de gratificación para este periodo y empleado.');
        }

        // Cálculo legal
        $calculo = $this->calcularGratificacion($empleado, $mesGrati, $anio);

        if ($calculo['dias_trabajados'] <= 0 || $calculo['gratificacion'] <= 0) {
            return redirect()
                ->route('boletas.gratificacion.index', ['tab' => 'empleados'])
                ->with('error', 'El empleado no tiene derecho a gratificación en este periodo.');
        }

        // Crear boleta
        $boleta = Boleta::create([
            'empleado_id'      => $empleado->id,
            'tipo'             => 'gratificacion',
            'periodo_mes'      => $mesGrati,
            'periodo_anio'     => $anio,
            'total_ingresos'   => $calculo['total_ingresos'],
            'total_descuentos' => 0,
            'total_aportes'    => $calculo['bonificacion_essalud'],
            'neto_pagar'       => $calculo['neto_pagar'],
            'estado'           => 'generado',
            'fecha_generacion' => now(),
        ]);

        // Detalle: gratificación legal
        DetalleBoleta::create([
            'boleta_id' => $boleta->id,
            'tipo'      => 'ingreso',
            'concepto'  => 'Gratificación legal ' . ($mesGrati === 7 ? 'Julio' : 'Diciembre'),
            'monto'     => $calculo['gratificacion'],
            'motivo'    => null,
        ]);

        // Detalle: bonificación Essalud
        DetalleBoleta::create([
            'boleta_id' => $boleta->id,
            'tipo'      => 'aporte',
            'concepto'  => 'Bonificación extraordinaria Essalud 9%',
            'monto'     => $calculo['bonificacion_essalud'],
            'motivo'    => null,
        ]);

        return redirect()
            ->route('boletas.gratificacion.index', ['tab' => 'generadas'])
            ->with('success', 'Boleta de gratificación generada correctamente.');
    }

    /**
     * Ver PDF de la boleta de gratificación
     */
    public function pdf($id)
    {
        $boleta = Boleta::with([
            'empleado.contratoActual.cargo',
        ])->where('tipo', 'gratificacion')->findOrFail($id);

        $empleado = $boleta->empleado;
        $mesGrati = (int) $boleta->periodo_mes;
        $anio     = (int) $boleta->periodo_anio;

        $calculo  = $this->calcularGratificacion($empleado, $mesGrati, $anio);

        $mesNombre = $mesGrati === 7 ? 'Julio' : 'Diciembre';

        // Nombre de archivo personalizado
        $nombreEmpleado = strtoupper($empleado->apellidos . ' ' . $empleado->nombres);
        $nombreEmpleado = str_replace(' ', '_', $nombreEmpleado);
        $nombreArchivo  = "{$nombreEmpleado}_Boleta_Gratificacion_{$anio}.pdf";

        return Pdf::loadView('boletas.gratificacion.pdf', [
            'boleta'        => $boleta,
            'empleado'      => $empleado,
            'mesNombre'     => $mesNombre,
            'anio'          => $anio,
            'calculo'       => $calculo,
        ])->stream($nombreArchivo);
    }

    /**
     * Cálculo legal de la gratificación (resumen)
     */
    protected function calcularGratificacion(Empleado $empleado, int $mesGrati, int $anio): array
    {
        $contrato = $empleado->contratoActual;

        if (!$contrato) {
            return [
                'base_remuneracion'   => 0,
                'dias_trabajados'     => 0,
                'meses_equivalentes'  => 0,
                'gratificacion'       => 0,
                'bonificacion_essalud'=> 0,
                'total_ingresos'      => 0,
                'neto_pagar'          => 0,
                'detalle_periodo'     => null,
            ];
        }

        // Base remuneración
        $sueldo       = (float) $contrato->sueldo;
        $asigFam      = $empleado->asignacion_familiar ? 102.50 : 0.00;
        $bonificacion = (float) ($empleado->bonificacion ?? 0);

        $baseRemuneracion = $sueldo + $asigFam + $bonificacion;

        // Semestre legal
        if ($mesGrati === 7) {
            $semestreInicio = Carbon::create($anio, 1, 1);
            $semestreFin    = Carbon::create($anio, 6, 30);
        } else {
            $semestreInicio = Carbon::create($anio, 7, 1);
            $semestreFin    = Carbon::create($anio, 12, 31);
        }

        // Rango real de trabajo dentro del semestre
        $fechaIngreso = Carbon::parse($contrato->fecha_inicio);

        // Si tuvo cese y es antes del final del semestre, se toma esa fecha
        if ($empleado->fecha_cese && $empleado->fecha_cese <= $semestreFin->toDateString()) {
            $fechaCese = Carbon::parse($empleado->fecha_cese);
        } elseif ($contrato->fecha_fin && $contrato->fecha_fin <= $semestreFin->toDateString()) {
            $fechaCese = Carbon::parse($contrato->fecha_fin);
        } else {
            $fechaCese = $semestreFin->copy();
        }

        // Inicio efectivo = máximo entre ingreso y comienzo del semestre
        $inicio = $fechaIngreso->greaterThan($semestreInicio) ? $fechaIngreso->copy() : $semestreInicio->copy();
        // Fin efectivo = mínimo entre cese (o fin contrato) y fin del semestre
        $fin = $fechaCese->lessThan($semestreFin) ? $fechaCese->copy() : $semestreFin->copy();

        if ($fin->lt($inicio)) {
            $diasTrabajados = 0;
        } else {
            $diasTrabajados = $inicio->diffInDays($fin) + 1;
        }

        // Meses equivalentes y gratificación legal
        $mesesEquivalentes = intdiv($diasTrabajados, 30);
        $diasResiduo       = $diasTrabajados % 30;

        // Fórmula legal aproximada: base * (meses/6 + días/30/6)
        $factor         = ($mesesEquivalentes + ($diasResiduo / 30)) / 6;
        $gratificacion  = round($baseRemuneracion * $factor, 2);

        // Bonificación extraordinaria Essalud 9% (no es descuento)
        $bonifEssalud   = round($gratificacion * 0.09, 2);

        $totalIngresos  = $gratificacion + $bonifEssalud;
        $netoPagar      = $totalIngresos; // no hay descuentos

        return [
            'base_remuneracion'    => round($baseRemuneracion, 2),
            'sueldo'               => round($sueldo, 2),
            'asignacion_familiar'  => round($asigFam, 2),
            'bonificacion_fija'    => round($bonificacion, 2),

            'semestre_inicio'      => $semestreInicio,
            'semestre_fin'         => $semestreFin,
            'inicio_efectivo'      => $inicio,
            'fin_efectivo'         => $fin,

            'dias_trabajados'      => $diasTrabajados,
            'meses_equivalentes'   => $mesesEquivalentes,
            'dias_residuo'         => $diasResiduo,

            'gratificacion'        => $gratificacion,
            'bonificacion_essalud' => $bonifEssalud,
            'total_ingresos'       => round($totalIngresos, 2),
            'neto_pagar'           => round($netoPagar, 2),
        ];
    }
}
