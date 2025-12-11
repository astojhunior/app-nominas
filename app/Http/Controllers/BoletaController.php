<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\DetalleBoleta;
use App\Models\Empleado;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class BoletaController extends Controller
{
    /**
     * Vista principal con las tarjetas del módulo Boletas
     */
    public function index()
    {
        return view('boletas.index');
    }

    /**
     * Vista para fin de mes:
     * pestaña 1: empleados
     * pestaña 2: selección de mes/año del empleado elegido
     */
    public function indexFinMes(Request $request)
{
    $empleados = Empleado::with('contratoActual.cargo')
    ->where('estado', 'activo')
    ->get();

    // Valor por defecto: colección vacía
    $boletas = collect();

    // Si está en pestaña "generadas", cargamos datos reales
    if ($request->tab === 'generadas') {

        $query = Boleta::with('empleado');

        if ($request->filled('mes')) {
            $query->where('periodo_mes', $request->mes);
        }

        if ($request->filled('anio')) {
            $query->where('periodo_anio', $request->anio);
        }

        $boletas = $query->orderBy('periodo_anio', 'desc')
                         ->orderBy('periodo_mes', 'desc')
                         ->get();
    }

    return view('boletas.fin_mes.index', [
        'empleados' => $empleados,
        'boletas'   => $boletas
    ]);
}
public function finMesConfirmar(Request $request)
{
    $request->validate([
        'empleado_id'  => 'required|exists:empleados,id',
        'periodo_mes'  => 'required|integer|min:1|max:12',
        'periodo_anio' => 'required|integer|min:2000',
    ]);

    $empleado = Empleado::findOrFail($request->empleado_id);

    // ✅ Fechas del mes seleccionado
    $inicioMes = \Carbon\Carbon::create($request->periodo_anio, $request->periodo_mes, 1)->startOfMonth();
    $finMes    = \Carbon\Carbon::create($request->periodo_anio, $request->periodo_mes, 1)->endOfMonth();
    $diasMes   = $inicioMes->daysInMonth;

    // ✅ Contar asistencias reales del mes
    $asistenciasMes = \App\Models\Asistencia::where('empleado_id', $empleado->id)
        ->whereBetween('fecha', [$inicioMes, $finMes])
        ->count();

    // Sin asistencias → NO continúa
    if ($asistenciasMes === 0) {
        return redirect()
            ->route('boletas.fin_mes.index', [
                'empleado_id' => $empleado->id,
                'tab' => 'periodo'
            ])
            ->with('error', 'El empleado no tiene asistencias registradas en este mes.');
    }

    // Incompleto → bloqueado
    if ($asistenciasMes < $diasMes) {
        return redirect()
            ->route('boletas.fin_mes.index', [
                'empleado_id' => $empleado->id,
                'tab' => 'periodo'
            ])
            ->with('error', "El empleado solo tiene $asistenciasMes de $diasMes asistencias este mes.");
    }

    // Nombre del mes
    $mesNombre = \Carbon\Carbon::createFromDate(null, $request->periodo_mes, 1)
        ->translatedFormat('F');

    // cálculos iniciales
    $sueldo_base = $empleado->contratoActual?->sueldo ?? 0;
    $asignacion_familiar = $empleado->asignacion_familiar ? 102.50 : 0;

    // descuentos enviados (o vacío)
    $descuentos = $request->input('descuentos', []);

    // total descuentos
    $total_descuentos = collect($descuentos)->sum('monto');

    // neto a pagar
    $neto_pagar = ($sueldo_base + $asignacion_familiar) - $total_descuentos;

    return view('boletas.fin_mes.confirmar', [
        'empleado' => $empleado,
        'periodo_mes' => $request->periodo_mes,
        'periodo_anio' => $request->periodo_anio,
        'mesNombre' => ucfirst($mesNombre),
        'sueldo_base' => $sueldo_base,
        'asignacion_familiar' => $asignacion_familiar,
        'descuentos' => $descuentos,
        'neto_pagar' => $neto_pagar
    ]);
}



    /**
     * Paso 2: validar datos, asistencia y mostrar resumen (confirmar)
     */
    public function finMesStore(Request $request)
    {
        $request->validate([
            'empleado_id'  => 'required|exists:empleados,id',
            'periodo_mes'  => 'required|integer|min:1|max:12',
            'periodo_anio' => 'required|integer|min:2000|max:2100',
        ]);

        $empleadoId   = (int) $request->empleado_id;
        $periodoMes   = (int) $request->periodo_mes;
        $periodoAnio  = (int) $request->periodo_anio;

        // Evitar boleta duplicada en el mismo periodo
        $existe = Boleta::where('empleado_id', $empleadoId)
            ->where('tipo', 'fin_mes')
            ->where('periodo_mes', $periodoMes)
            ->where('periodo_anio', $periodoAnio)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ya existe una boleta de fin de mes para este empleado en ese periodo.');
        }

        // Validar asistencia: todos los días del mes registrados
        if (!$this->tieneAsistenciaCompleta($empleadoId, $periodoMes, $periodoAnio)) {
            return back()->with('error', 'El empleado no tiene registradas todas las asistencias del mes seleccionado.');
        }

        $empleado = Empleado::with('contratoActual')->findOrFail($empleadoId);

        if (!$empleado->contratoActual || !$empleado->contratoActual->sueldo) {
            return back()->with('error', 'El empleado no tiene contrato activo con sueldo registrado.');
        }

        // Cálculo completo (40% + 60%, asignación, bonificación, AFP/ONP)
        $calculo = $this->calcularBoletaFinMes($empleado, $periodoMes, $periodoAnio);

        $mesNombre = Carbon::create($periodoAnio, $periodoMes, 1)
            ->translatedFormat('F');

        return view('boletas.fin_mes.confirmar', [
            'empleado'            => $empleado,
            'periodo_mes'         => $periodoMes,
            'periodo_anio'        => $periodoAnio,
            'mesNombre'           => ucfirst($mesNombre),
            'sueldo_base'         => $calculo['sueldo_base'],
            'asignacion_familiar' => $calculo['asignacion_familiar'],
            'bonificacion'        => $calculo['bonificacion'],
            'ingresos'            => $calculo['ingresos'],
            'descuentos'          => $calculo['descuentos'],
            'total_ingresos'      => $calculo['total_ingresos'],
            'total_descuentos'    => $calculo['total_descuentos'],
            'neto_pagar'          => $calculo['neto_pagar'],
        ]);
    }

    /**
     * Paso 3: generar boleta y detalles en base al cálculo
     */
    public function finMesGenerar(Request $request)
    
    {
        $request->validate([
            'empleado_id'  => 'required|exists:empleados,id',
            'periodo_mes'  => 'required|integer|min:1|max:12',
            'periodo_anio' => 'required|integer|min:2000|max:2100',
        ]);
        $descuentosFormulario = $request->input('descuentos', []);

        $empleado   = Empleado::with('contratoActual')->findOrFail($request->empleado_id);
        $periodoMes = (int) $request->periodo_mes;
        $periodoAnio = (int) $request->periodo_anio;

        // Revalidar que no exista boleta duplicada
        $existe = Boleta::where('empleado_id', $empleado->id)
            ->where('tipo', 'fin_mes')
            ->where('periodo_mes', $periodoMes)
            ->where('periodo_anio', $periodoAnio)
            ->exists();

        if ($existe) {
            return redirect()
                ->route('boletas.fin_mes.index')
                ->with('error', 'Ya existe una boleta de fin de mes para este empleado en ese periodo.');
        }


        // Recalcular montos para guardar (mismo método que en confirmar)
        $calculo = $this->calcularBoletaFinMes($empleado, $periodoMes, $periodoAnio);

        $totalDescuentosExtra = collect($descuentosFormulario)->sum('monto');

        $calculo['total_descuentos'] += $totalDescuentosExtra;
        $calculo['neto_pagar'] = $calculo['total_ingresos']
                                - $calculo['total_descuentos'];
        // Crear boleta
        $boleta = Boleta::create([
            'empleado_id'      => $empleado->id,
            'tipo'             => 'fin_mes',
            'periodo_mes'      => $periodoMes,
            'periodo_anio'     => $periodoAnio,
            'total_ingresos'   => $calculo['total_ingresos'],
            'total_descuentos' => $calculo['total_descuentos'],
            'total_aportes'    => 0,
            'neto_pagar'       => $calculo['neto_pagar'],
            'estado'           => 'generado',
            'fecha_generacion' => now(),
        ]);

        // Crear detalles: ingresos
        foreach ($descuentosFormulario as $d) {
        if (!empty($d['monto'])) {
        DetalleBoleta::create([
            'boleta_id' => $boleta->id,
            'tipo'      => 'descuento',
            'concepto'  => 'Descuento adicional',
            'monto'     => -abs($d['monto']),
            'motivo'    => $d['motivo'] ?? null,
            ]);
             }
         }

        // Crear detalles: descuentos
        foreach ($calculo['descuentos'] as $desc) {
            DetalleBoleta::create([
                'boleta_id' => $boleta->id,
                'tipo'      => 'descuento',
                'concepto'  => $desc['concepto'],
                'monto'     => $desc['monto'],
                'motivo'    => $desc['motivo'] ?? null,
            ]);
        }

        return redirect()
            // más adelante apuntará a una lista de boletas; por ahora volvemos a fin de mes
            ->route('boletas.fin_mes.index')
            ->with('success', 'Boleta de fin de mes generada correctamente.');
    }

    /**
     * Generar y mostrar PDF en navegador
     */
public function pdf($id)
{
    $boleta = Boleta::with([
        'empleado.contratoActual.cargo',
        'detalles',
    ])->findOrFail($id);

    $empleado = $boleta->empleado;
    $detalles = $boleta->detalles;

    // Recalcular montos
    $calculo = $this->calcularBoletaFinMes(
        $empleado,
        (int) $boleta->periodo_mes,
        (int) $boleta->periodo_anio
    );

    // Nombre del mes
    $mesNombre = Carbon::createFromDate(
        $boleta->periodo_anio,
        $boleta->periodo_mes,
        1
    )->translatedFormat('F');

    // Separar 40% y 60%
    $quincena = 0;
    $finMesBruto = 0;

    foreach ($calculo['ingresos'] as $ing) {
        if (stripos($ing['concepto'], '40%') !== false) {
            $quincena = $ing['monto'];
        }
        if (stripos($ing['concepto'], '60%') !== false) {
            $finMesBruto = $ing['monto'];
        }
    }

    // Ingresos adicionales
    $ingresosAdicionales = collect($calculo['ingresos'])
        ->filter(fn($i) =>
            !str_contains($i['concepto'], '40%') &&
            !str_contains($i['concepto'], '60%')
        )->values();

    // Descuentos adicionales
    $descuentosAdicionales = $detalles
        ->where('tipo', 'descuento')
        ->filter(function ($d) {
            return !(
                str_contains(strtoupper($d->concepto), 'AFP') ||
                str_contains(strtoupper($d->concepto), 'ONP')
            );
        })
        ->map(function ($d) {
            return [
                'motivo' => $d->motivo ?: 'Descuento adicional',
                'monto'  => abs($d->monto),
            ];
        });

    $totalDescuentosAdicionales = $descuentosAdicionales->sum('monto');

    // AFP
    $afpAporte = 0;
    foreach ($calculo['descuentos'] as $desc) {
        if (
            str_contains(strtoupper($desc['concepto']), 'AFP') ||
            str_contains(strtoupper($desc['concepto']), 'ONP')
        ) {
            $afpAporte = $desc['monto'];
        }
    }

    // Fin de mes neto
    $finMesNeto = max(0, $finMesBruto - $totalDescuentosAdicionales);

    // Total descuentos finales
    $totalDescuentos = $calculo['total_descuentos'] + $totalDescuentosAdicionales;

    // Neto real
    $netoReal = $calculo['total_ingresos'] - $totalDescuentos;

    // --------------------------------------
    // NOMBRE DE ARCHIVO PERSONALIZADO
    // --------------------------------------
    $nombreEmpleado = str_replace(' ', '_', strtoupper($empleado->apellidos . ' ' . $empleado->nombres));
    $nombreMes = ucfirst($mesNombre);
    $anio = $boleta->periodo_anio;

    $nombreArchivo = "{$nombreEmpleado}_boleta_{$nombreMes}_{$anio}.pdf";

    // --------------------------------------
    // ENVIAR AL PDF (inline Y CON NOMBRE)
    // --------------------------------------
    return response(
        Pdf::loadView('boletas.fin_mes.pdf', [
            'boleta'               => $boleta,
            'empleado'             => $empleado,
            'mesNombre'            => ucfirst($mesNombre),
            'periodo_anio'         => $boleta->periodo_anio,

            'quincena'             => $quincena,
            'finMes'               => $finMesNeto,
            'asignacionFamiliar'   => $calculo['asignacion_familiar'],
            'ingresos_adicionales' => $ingresosAdicionales,

            'afp_aporte'           => $afpAporte,
            'descuentos_adicionales' => $descuentosAdicionales,
            'total_descuentos'     => $totalDescuentos,

            'essalud'              => $calculo['essalud'],

            'total_ingresos'       => $calculo['total_ingresos'],
            'neto'                 => $netoReal,
        ])->output(),
        200,
        [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$nombreArchivo}\"",
        ]
    );
}




    /**
     * Verifica que el empleado tenga todas las asistencias del mes registradas
     */
    protected function tieneAsistenciaCompleta(int $empleadoId, int $mes, int $anio): bool
    {
        $inicio = Carbon::create($anio, $mes, 1)->startOfDay();
        $fin    = (clone $inicio)->endOfMonth()->endOfDay();

        $diasMes = $inicio->daysInMonth;

        $totalRegistros = Asistencia::where('empleado_id', $empleadoId)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->count();

        // Si tiene al menos un registro por día del mes, se considera completo
        return $totalRegistros >= $diasMes;
    }

    /**
     * Cálculo real de la boleta de fin de mes:
     *  - 40% quincena
     *  - 60% fin de mes
     *  - asignación familiar
     *  - bonificación fija
     *  - AFP/ONP solo sobre el 60%
     */
    protected function calcularBoletaFinMes(Empleado $empleado, int $mes, int $anio): array
{
    $contrato = $empleado->contratoActual;

    $sueldoBase = (float) $contrato->sueldo;

    // 40% y 60%
    $montoQuincena = round($sueldoBase * 0.40, 2);
    $montoFinMes   = round($sueldoBase * 0.60, 2);

    // VALIDAR ASISTENCIAS COMPLETAS
    $totalDiasMes = \Carbon\Carbon::create($anio, $mes)->daysInMonth;

    $asistenciasCompletas = \App\Models\Asistencia::where('empleado_id', $empleado->id)
        ->whereMonth('fecha', $mes)
        ->whereYear('fecha', $anio)
        ->count();

    if ($asistenciasCompletas < $totalDiasMes) {
        throw new \Exception("El empleado no tiene todas las asistencias completas del mes.");
    }

    // Asignación familiar
    $asignacionFamiliar = $empleado->asignacion_familiar ? 102.50 : 0.00;

    // Bonificación fija (si la manejas así)
    $bonificacion = $empleado->bonificacion ?? 0;

    // === INGRESOS ===
    $ingresos = [];
    $totalIngresos = 0;

    $addIngreso = function (string $concepto, float $monto) use (&$ingresos, &$totalIngresos) {
        if ($monto <= 0) return;

        $ingresos[] = [
            'concepto' => $concepto,
            'monto'    => round($monto, 2),
        ];

        $totalIngresos += round($monto, 2);
    };

    // Sueldo 40% + 60%
    $addIngreso('Sueldo quincena (40%)', $montoQuincena);
    $addIngreso('Sueldo fin de mes (60%)', $montoFinMes);

    // Asignación familiar
    $addIngreso('Asignación familiar', $asignacionFamiliar);

    // Bonificación fija
    $addIngreso('Bonificación', $bonificacion);

    // === DESCUENTOS ===
    $descuentos = [];
    $totalDescuentos = 0;

    $addDescuento = function (string $concepto, float $monto, ?string $motivo = null)
        use (&$descuentos, &$totalDescuentos) {

        if ($monto <= 0) return;

        $descuentos[] = [
            'concepto' => $concepto,
            'monto'    => round($monto, 2),
            'motivo'   => $motivo,
        ];

        $totalDescuentos += round($monto, 2);
    };

    // AFP / ONP solo sobre el 60%
    $sistema = $contrato->sistema_pension;
    $afpNombre = $contrato->afp_nombre ?? null;

    $porcentaje = 0;
    $conceptoPension = null;

    if ($sistema === 'ONP') {
        $porcentaje = 0.13;
        $conceptoPension = 'ONP';
    } elseif ($sistema === 'AFP') {
        $porcentaje = 0.10;
        $conceptoPension = 'AFP' . ($afpNombre ? ' ' . $afpNombre : '');
    }

    if ($porcentaje > 0 && $conceptoPension) {
        $montoPension = round($montoFinMes * $porcentaje, 2);
        $addDescuento($conceptoPension, $montoPension);
    }

    // === APORTES === (NO descontables)
    $essalud = round($sueldoBase * 0.09, 2);

    $netoPagar = $totalIngresos - $totalDescuentos;

    return [
        'sueldo_base'         => $sueldoBase,
        'asignacion_familiar' => $asignacionFamiliar,
        'bonificacion'        => $bonificacion,

        'ingresos'            => $ingresos,
        'descuentos'          => $descuentos,

        'total_ingresos'      => round($totalIngresos, 2),
        'total_descuentos'    => round($totalDescuentos, 2),

        'essalud'             => $essalud, // ✅ añadido
        'neto_pagar'          => round($netoPagar, 2),
    ];
}
public function eliminar($id)
{
    $boleta = Boleta::findOrFail($id);

    $boleta->detalles()->delete();
    $boleta->delete();

    return back()->with('success', 'Boleta eliminada correctamente.');
}

}
