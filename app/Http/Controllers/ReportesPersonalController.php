<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport; // si deseas usar Excel personalizado

class ReportesPersonalController extends Controller
{
    /* ============================================
     *  INDEX GENERAL DE CATEGORÍAS
     * ============================================
    */
    public function index()
    {
        return view('reportes.index');
    }



    /* ============================================
     *   1. EMPLEADOS ACTIVOS (WEB)
     * ============================================
    */
    public function activos()
    {
        $empleados = Empleado::with(['contrato.area', 'contrato.cargo'])
            ->where('estado', 'activo')
            ->paginate(20);

        $areas  = Area::all();
        $cargos = Cargo::all();

        // Conteos para gráficos
        $conteoPorArea = [];
        foreach ($areas as $area) {
            $conteoPorArea[] = Empleado::where('estado', 'activo')
                ->whereHas('contrato', fn($q) => $q->where('area_id', $area->id))
                ->count();
        }

        $sexoMasculino = Empleado::where('estado','activo')->where('sexo','masculino')->count();
        $sexoFemenino  = Empleado::where('estado','activo')->where('sexo','femenino')->count();

        return view('reportes.personal.activos', compact(
            'empleados','areas','cargos',
            'conteoPorArea','sexoMasculino','sexoFemenino'
        ));
    }



    /* ============================================
     *  1A. PDF EMPLEADOS ACTIVOS
     * ============================================
    */


    /* ============================================
     *   2. EMPLEADOS CESADOS (WEB)
     * ============================================
    */
  public function cesados()
{
    $empleados = Empleado::with(['contrato.area','contrato.cargo','renuncia'])
    ->whereHas('contrato', function ($q) {
        $q->where('estado_contrato', 'rescindido');
    })
    ->paginate(20);

    $areas  = Area::all();
    $cargos = Cargo::all();

    // Gráfico ceses por mes
    $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $cesesPorMes = [];

    for ($i = 1; $i <= 12; $i++) {

        $cesesPorMes[] = Empleado::whereHas('contrato', function ($q) {
                $q->where('estado_contrato', 'rescindido');
            })
            ->whereMonth('fecha_cese', $i)
            ->count();
    }

    // Ceses por área
    $cesesPorArea = [];
    foreach ($areas as $area) {
        $cesesPorArea[] = Empleado::whereHas('contrato', function ($q) use ($area) {
                $q->where('estado_contrato', 'rescindido')
                  ->where('area_id', $area->id);
            })
            ->count();
    }

    return view('reportes.personal.cesados', compact(
        'empleados','areas','cargos',
        'meses','cesesPorMes','cesesPorArea'
    ));
}




    /* ============================================
     *  ███  3. REPORTE POR ÁREA (WEB)
     * ============================================
    */
    public function porArea()
    {
        $areas = Area::all();
        $empleados = Empleado::with(['contrato.area','contrato.cargo'])->paginate(20);

        $totalEmpleados = Empleado::count();

        // Conteo por área
        $conteoPorArea = [];
        foreach ($areas as $area) {
            $conteoPorArea[] = Empleado::whereHas('contrato', fn($q)=>$q->where('area_id',$area->id))->count();
        }

        // Área mayor
        $areaMayor = $areas->count() ? 
            $areas[$this->indexMax($conteoPorArea)]->nombre : null;

        return view('reportes.personal.por-area', compact(
            'areas','empleados','conteoPorArea','totalEmpleados','areaMayor'
        ));
    }


    /* ============================================
     *  3A. PDF POR ÁREA
     * ============================================
    */
    

    /* ============================================
     *  ███  4. REPORTE POR CARGO (WEB)
     * ============================================
    */
    public function porCargo()
    {
        $cargos = Cargo::all();
        $empleados = Empleado::with(['contrato.area','contrato.cargo'])->paginate(20);

        $totalEmpleados = Empleado::count();

        $conteoPorCargo = [];
        foreach ($cargos as $cargo) {
            $conteoPorCargo[] = Empleado::whereHas('contrato', fn($q)=>$q->where('cargo_id',$cargo->id))->count();
        }

        $cargoMayor = $cargos->count() ?
            $cargos[$this->indexMax($conteoPorCargo)]->cargo : null;

        return view('reportes.personal.por-cargo', compact(
            'cargos','empleados','conteoPorCargo','totalEmpleados','cargoMayor'
        ));
    }



    /* ============================================
     *  Extra: función para encontrar índice del mayor
     * ============================================
    */
    private function indexMax($array)
    {
        if (count($array) == 0) return 0;
        return array_keys($array, max($array))[0];
    }
}
