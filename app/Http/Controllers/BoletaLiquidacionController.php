<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Empleado;
use App\Models\Asistencia;
use App\Models\Renuncia;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;

class BoletaLiquidacionController extends Controller
{
    /**
     * Vista principal con tabs
     */
    public function index()
    {
        // 1. Empleados en estado BAJA
        $empleados = Empleado::with('contratoActual.cargo')
            ->where('estado', 'baja')
            ->get();

        // 2. Filtros del request
        $search = request('search');
        $mes    = request('mes');
        $anio   = request('anio');

        // 3. Query base de boletas
        $query = Boleta::with(['empleado.contratoActual.cargo'])
            ->where('tipo', 'liquidacion')
            ->orderBy('fecha_generacion', 'desc');

        // 4. Filtro por buscador
        if (!empty($search)) {

            $query->whereHas('empleado', function($q) use ($search) {

                $q->where('dni', 'LIKE', "%$search%")
                  ->orWhere('nombres', 'LIKE', "%$search%")
                  ->orWhere('apellidos', 'LIKE', "%$search%")
                  ->orWhereHas('contratoActual.cargo', function($c) use ($search) {
                        $c->where('cargo', 'LIKE', "%$search%");
                  });

            });
        }

        // 5. Filtro por mes
        if (!empty($mes)) {
            $query->whereMonth('fecha_generacion', $mes);
        }

        // 6. Filtro por año
        if (!empty($anio)) {
            $query->whereYear('fecha_generacion', $anio);
        }

        // 7. Ejecutar consulta
        $boletas = $query->get();

        // 8. Lista de meses para el select del filtro
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return view('boletas.liquidacion.index', [
            'empleados' => $empleados,
            'boletas'   => $boletas,
            'meses'     => $meses,
            'search'    => $search,
            'mes'       => $mes,
            'anio'      => $anio
        ]);
    }

    /**
     * Validar datos y mostrar la vista de confirmación + cálculo
     */
    public function confirmar()
    {
        request()->validate([
            'empleado_id' => 'required|exists:empleados,id',
        ]);

        $empleado = Empleado::with('contratoActual.cargo')->findOrFail(request('empleado_id'));

        // Verificar que tenga contrato actual
        $contrato = $empleado->contratoActual;
        if (!$contrato) {
            return back()->with('error', 'El empleado no tiene un contrato actual registrado.');
        }

        // Verificar fecha de inicio en contrato actual
        if (empty($contrato->fecha_inicio)) {
            return back()->with('error', 'El contrato actual no tiene fecha de inicio registrada.');
        }

        // ===============================
        // 1) FECHAS: ingreso y cese REAL
        // ===============================
        $ingreso = Carbon::parse($contrato->fecha_inicio)->startOfDay();

        // Última renuncia registrada
        $renuncia = $empleado->renuncias()
            ->where('estado', 'registrada')
            ->latest()
            ->first();

        if ($renuncia) {
            if (empty($renuncia->fecha_cese)) {
                return back()->with('error', 'La renuncia encontrada no tiene fecha de cese.');
            }
            $cese = Carbon::parse($renuncia->fecha_cese)->endOfDay();
        } else {
            if (empty($empleado->fecha_cese)) {
                return back()->with('error', 'El empleado no tiene fecha de cese registrada.');
            }
            $cese = Carbon::parse($empleado->fecha_cese)->endOfDay();
        }

        // Copias fijas para cálculos
        $ingresoFijo = $ingreso->copy();
        $ceseFijo    = $cese->copy();

        // ===============================
        // 2) DÍAS TRABAJADOS REALES
        // ===============================
        $diasTrabajadosReales = Asistencia::where('empleado_id', $empleado->id)
            ->where('estado_asistencia', 'turno')
            ->whereBetween('fecha', [$ingreso->toDateString(), $cese->toDateString()])
            ->count();

        // ===============================
        // 3) SUELDO + ASIGNACIÓN
        // ===============================
        $sueldo = $contrato->sueldo ?? 0;
        $asignacion = $empleado->asignacion_familiar ? 102.50 : 0;
        $base = $sueldo + $asignacion;

        // ===============================
        // 4) ANTIGÜEDAD EXACTA (ley peruana)
        // ===============================
        $diasTotal = $ingresoFijo->diffInDays($ceseFijo) + 1;
        $antiguedadMeses = intdiv($diasTotal, 30);
        $antiguedadDias  = $diasTotal % 30;

        // ===============================
        // 5) VACACIONES + CTS
        // ===============================
        $vacaciones = round(($base / 12) * $antiguedadMeses + ($base / 360) * $antiguedadDias, 2);
        $cts        = round(($base / 12) * $antiguedadMeses + ($base / 360) * $antiguedadDias, 2);

        // ===============================
        // 6) GRATIFICACIÓN TRUNCA – SOLO MESES COMPLETOS
        // ===============================
        $mesCese = $ceseFijo->month;
        $inicioSemestre = $mesCese <= 6
            ? Carbon::create($ceseFijo->year, 1, 1)
            : Carbon::create($ceseFijo->year, 7, 1);

        $mesesSemestre = 0;
        $current = $inicioSemestre->copy()->startOfMonth();

        while ($current <= $ceseFijo) {

            $inicioMes = $current->copy()->startOfMonth();
            $finMes    = $current->copy()->endOfMonth();

            // Cuenta SOLO si trabajó TODO el mes
            if ($ingresoFijo <= $inicioMes && $ceseFijo >= $finMes) {
                $mesesSemestre++;
            }

            $current->addMonth();
        }

        $gratificacion = round(($base / 6) * $mesesSemestre, 2);

        // ===============================
        // 7) REMUNERACIÓN ÚLTIMO MES (solo días trabajados)
        // ===============================
        $diasUltimoMes = Asistencia::where('empleado_id', $empleado->id)
            ->whereMonth('fecha', $ceseFijo->month)
            ->where('estado_asistencia', 'turno')
            ->count();

        $diasDelMes = $ceseFijo->daysInMonth ?: 30;
        $remuneracionUltimoMes = round(($sueldo / $diasDelMes) * $diasUltimoMes, 2);

        // ===============================
        // 8) SI NO TRABAJÓ NADA → LIQUIDACIÓN = 0
        // ===============================
        if ($diasTrabajadosReales === 0) {
            $vacaciones = 0;
            $cts = 0;
            $gratificacion = 0;
            $remuneracionUltimoMes = 0;
        }

        // ===============================
        // 9) TOTAL
        // ===============================
        $totalLiquidacion = round(
            $vacaciones + $cts + $gratificacion + $remuneracionUltimoMes,
            2
        );

        return view('boletas.liquidacion.confirmar', compact(
            'empleado',
            'ingresoFijo',
            'ceseFijo',
            'sueldo',
            'asignacion',
            'base',
            'antiguedadMeses',
            'antiguedadDias',
            'vacaciones',
            'cts',
            'gratificacion',
            'diasUltimoMes',
            'remuneracionUltimoMes',
            'totalLiquidacion',
            'diasTrabajadosReales',
            'mesesSemestre'
        ));
    }

    /**
     * Guardar boleta en BD
     */
    public function generar()
    {
        request()->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'total'       => 'required|numeric',
        ]);

        $data = request()->all();

        $boleta = Boleta::create([
            'empleado_id'      => Arr::get($data, 'empleado_id'),
            'tipo'             => 'liquidacion',
            'periodo_mes'      => null,
            'periodo_anio'     => now()->year,

            // Datos generales
            'fecha_ingreso'          => Arr::get($data, 'fecha_ingreso'),
            'fecha_cese'             => Arr::get($data, 'fecha_cese'),
            'antiguedad_texto'       => Arr::get($data, 'antiguedad_texto'),
            'dias_trabajados_reales' => Arr::get($data, 'dias_trabajados_reales'),

            // Remuneración computable
            'sueldo'       => Arr::get($data, 'sueldo', 0),
            'asignacion'   => Arr::get($data, 'asignacion', 0),
            'base'         => Arr::get($data, 'base', 0),

            // Vacaciones truncas
            'vac_meses'        => Arr::get($data, 'vac_meses', 0),
            'vac_dias'         => Arr::get($data, 'vac_dias', 0),
            'monto_vacaciones' => Arr::get($data, 'monto_vacaciones', 0),

            // CTS trunca
            'cts_meses'    => Arr::get($data, 'cts_meses', 0),
            'cts_dias'     => Arr::get($data, 'cts_dias', 0),
            'monto_cts'    => Arr::get($data, 'monto_cts', 0),

            // Gratificación trunca
            'grati_meses'  => Arr::get($data, 'grati_meses', 0),
            'monto_grati'  => Arr::get($data, 'monto_grati', 0),

            // Último mes
            'dias_ultimo_mes' => Arr::get($data, 'dias_ultimo_mes', 0),
            'monto_dias_mes'  => Arr::get($data, 'monto_dias_mes', 0),

            // Total
            'total_liquidacion' => Arr::get($data, 'total', 0),

            // Campos contables
            'total_ingresos'   => Arr::get($data, 'total', 0),
            'total_descuentos' => 0,
            'total_aportes'    => 0,
            'neto_pagar'       => Arr::get($data, 'total', 0),

            'estado'           => 'generado',
            'fecha_generacion' => now(),
        ]);

        return redirect()
            ->route('boletas.liquidacion.index', ['tab' => 'generadas'])
            ->with('success', 'Boleta de liquidación generada correctamente.');
    }

    /**
     * Generar PDF
     *
     * Ahora: si la boleta ya tiene montos guardados, el PDF usa esos valores.
     * Si faltan valores en la boleta, se reconstruyen los cálculos.
     */
    public function pdf($id)
    {
        $boleta = Boleta::with('empleado.contratoActual.cargo')->findOrFail($id);
        $empleado = $boleta->empleado;

        // Verificar contrato actual (necesario para reconstruir cálculos si faltan datos)
        $contrato = $empleado->contratoActual;
        if (!$contrato && (
            is_null($boleta->fecha_ingreso) || is_null($boleta->fecha_cese)
        )) {
            // No hay contrato y boleta tampoco tiene fechas: no podemos reconstruir
            abort(404, 'El empleado no tiene contrato actual y la boleta no contiene fechas. Imposible generar PDF.');
        }

        // Preferimos usar las fechas guardadas en la boleta (si existen),
        // si no, intentamos reconstruir desde contrato/empleado.
        if (!empty($boleta->fecha_ingreso)) {
            $ingresoFijo = Carbon::parse($boleta->fecha_ingreso)->startOfDay();
        } else {
            // contrato debe existir aquí
            if (!$contrato || empty($contrato->fecha_inicio)) {
                abort(404, 'No hay fecha de ingreso disponible para generar el PDF.');
            }
            $ingresoFijo = Carbon::parse($contrato->fecha_inicio)->startOfDay();
        }

        if (!empty($boleta->fecha_cese)) {
            $ceseFijo = Carbon::parse($boleta->fecha_cese)->endOfDay();
        } else {
            // preferir renuncia si existe
            $renuncia = $empleado->renuncias()
                ->where('estado', 'registrada')
                ->latest()
                ->first();

            if ($renuncia && !empty($renuncia->fecha_cese)) {
                $ceseFijo = Carbon::parse($renuncia->fecha_cese)->endOfDay();
            } elseif (!empty($empleado->fecha_cese)) {
                $ceseFijo = Carbon::parse($empleado->fecha_cese)->endOfDay();
            } else {
                abort(404, 'No hay fecha de cese disponible para generar el PDF.');
            }
        }

        // cálculos base (si boleta ya tiene montos, los usaremos como preferencia)
        $sueldo = $boleta->sueldo ?? ($contrato->sueldo ?? 0);
        $asignacion = $boleta->asignacion ?? ($empleado->asignacion_familiar ? 102.50 : 0);
        $base = $boleta->base ?? ($sueldo + $asignacion);

        // Antigüedad (reconstruir siempre para mostrar texto si boleta no tiene)
        $diasTotal = $ingresoFijo->diffInDays($ceseFijo) + 1;
        $antiguedadMeses = intdiv($diasTotal, 30);
        $antiguedadDias  = $diasTotal % 30;
        $antiguedadTexto = $boleta->antiguedad_texto ?? "{$antiguedadMeses} meses, {$antiguedadDias} días";

        // Vacaciones: preferir monto guardado en boleta, sino calcular
        $vacaciones = !is_null($boleta->monto_vacaciones) ? $boleta->monto_vacaciones
            : round(($base / 12) * $antiguedadMeses + ($base / 360) * $antiguedadDias, 2);

        // CTS: preferir monto guardado
        $cts = !is_null($boleta->monto_cts) ? $boleta->monto_cts
            : round(($base / 12) * $antiguedadMeses + ($base / 360) * $antiguedadDias, 2);

        // Gratificación: preferir boleta
        // Para reconstruir mesesSemestre hacemos el mismo proceso
        $mesCese = $ceseFijo->month;
        $inicioSemestre = $mesCese <= 6
            ? Carbon::create($ceseFijo->year, 1, 1)
            : Carbon::create($ceseFijo->year, 7, 1);

        $mesesSemestre = 0;
        $current = $inicioSemestre->copy()->startOfMonth();
        while ($current <= $ceseFijo) {
            $inicioMes = $current->copy()->startOfMonth();
            $finMes    = $current->copy()->endOfMonth();

            if ($ingresoFijo <= $inicioMes && $ceseFijo >= $finMes) {
                $mesesSemestre++;
            }

            $current->addMonth();
        }

        $gratificacion = !is_null($boleta->monto_grati) ? $boleta->monto_grati
            : round(($base / 6) * $mesesSemestre, 2);

        // Días trabajados último mes: preferir boleta
        $diasUltimoMes = !is_null($boleta->dias_ultimo_mes) ? $boleta->dias_ultimo_mes
            : Asistencia::where('empleado_id', $empleado->id)
                ->whereMonth('fecha', $ceseFijo->month)
                ->where('estado_asistencia', 'turno')
                ->count();

        $diasDelMes   = $ceseFijo->daysInMonth ?: 30;
        $montoDiasMes = !is_null($boleta->monto_dias_mes) ? $boleta->monto_dias_mes
            : round(($sueldo / $diasDelMes) * $diasUltimoMes, 2);

        // Total: preferir boleta
        $totalLiquidacion = !is_null($boleta->total_liquidacion) ? $boleta->total_liquidacion
            : round($vacaciones + $cts + $gratificacion + $montoDiasMes, 2);

        // Nombre archivo
        $nombre = strtoupper(str_replace(' ', '_', ($empleado->apellidos ?? '') . '_' . ($empleado->nombres ?? '')));
        $nombreArchivo = "{$nombre}_LIQUIDACION_" . date('Y') . ".pdf";

        // Preparar datos que la vista espera (mismos keys que usabas antes)
        $viewData = [
            'empleado' => $empleado,
            'boleta' => $boleta,
            'sueldo' => $sueldo,
            'asignacion' => $asignacion,
            'base' => $base,
            'antiguedadMeses' => $antiguedadMeses,
            'antiguedadDias' => $antiguedadDias,
            'antiguedadTexto' => $antiguedadTexto,
            'vac_meses' => $antiguedadMeses,
            'vac_dias' => $antiguedadDias,
            'monto_vacaciones' => $vacaciones,
            'cts_meses' => $antiguedadMeses,
            'cts_dias' => $antiguedadDias,
            'monto_cts' => $cts,
            'grati_meses' => $mesesSemestre,
            'monto_grati' => $gratificacion,
            'dias_ultimo_mes' => $diasUltimoMes,
            'monto_dias_mes' => $montoDiasMes,
            'total_liquidacion' => $totalLiquidacion,
            'fecha_ingreso' => $ingresoFijo->toDateString(),
            'fecha_cese' => $ceseFijo->toDateString(),
            'dias_trabajados_reales' => $boleta->dias_trabajados_reales ?? Asistencia::where('empleado_id', $empleado->id)
                ->where('estado_asistencia', 'turno')
                ->whereBetween('fecha', [$ingresoFijo->toDateString(), $ceseFijo->toDateString()])
                ->count()
        ];

        return Pdf::loadView('boletas.liquidacion.pdf', $viewData)->stream($nombreArchivo);
    }

    public function destroy($id)
    {
        $request = request();

        // Validar password
        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth('admin')->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }

        $boleta = Boleta::where('tipo', 'liquidacion')->findOrFail($id);
        $boleta->delete();

        return redirect()
            ->route('boletas.liquidacion.index', ['tab' => 'generadas'])
            ->with('success', 'Boleta eliminada correctamente.');
    }
}
