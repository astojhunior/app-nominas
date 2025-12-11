<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\Renuncia;
use App\Models\Sancion;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
{
    // Valores por defecto → mes y año actual
    $mes = $request->mes ?? now()->month;
    $anio = $request->anio ?? now()->year;

    // Solo permitir 2024 → 2027
    if ($anio < 2024 || $anio > 2027) {
        $anio = now()->year;
    }

    // Días del mes seleccionado
    $fecha = \Carbon\Carbon::create($anio, $mes, 1);
    $diasMes = $fecha->daysInMonth;

    // Nombre del mes en español
    $mesNombre = $fecha->locale('es')->translatedFormat('F Y');

    // Labels del eje X
    $labelsMes = range(1, $diasMes);

    // Datos
    $asistenciasDias = [];
    $renunciasDias   = [];
    $sancionesDias   = [];

    for ($dia = 1; $dia <= $diasMes; $dia++) {

        $asistenciasDias[] = Asistencia::whereDay('fecha', $dia)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->count();

        $renunciasDias[] = Renuncia::whereDay('fecha_renuncia', $dia)
            ->whereMonth('fecha_renuncia', $mes)
            ->whereYear('fecha_renuncia', $anio)
            ->count();

        $sancionesDias[] = Sancion::whereDay('fecha_aplicacion', $dia)
            ->whereMonth('fecha_aplicacion', $mes)
            ->whereYear('fecha_aplicacion', $anio)
            ->count();
    }

    return view('admin.dashboard', compact(
        'labelsMes',
        'asistenciasDias',
        'renunciasDias',
        'sancionesDias',
        'mesNombre',
        'mes',
        'anio'
    ));
}

}
