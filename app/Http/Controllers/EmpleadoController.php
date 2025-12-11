<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrato;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmpleadoController extends Controller
{
    public function create()
    {
        $areas = Area::all();
        $cargos = Cargo::with('area')->get();
        return view('rrhh.empleados.create', compact('areas', 'cargos'));
    }

public function index(Request $request)
{
    $query = Empleado::with([
        'contratoActual.area',
        'contratoActual.cargo',
    ]);

    /* ============================
        BUSCAR  
       ============================ */
    if ($request->filled('buscar')) {
        $buscar = $request->buscar;

        $query->where(function ($q) use ($buscar) {
            $q->where('nombres', 'like', "%$buscar%")
              ->orWhere('apellidos', 'like', "%$buscar%")
              ->orWhere('dni', 'like', "%$buscar%")
              ->orWhere('correo', 'like', "%$buscar%");
        });
    }

    /* ============================
       FILTRO POR CARGO  
       ============================ */
    if ($request->filled('cargo')) {
        $query->whereHas('contratoActual', function ($q) use ($request) {
            $q->where('cargo_id', $request->cargo);
        });
    }

    /* ============================
       FILTRO POR ESTADO  
       activos / inactivos / todos
       ============================ */
    $estado = $request->estado ?? 'activos';

    if ($estado === 'activos') {
        $query->whereHas('contratoActual');   // tiene contrato
    }
    elseif ($estado === 'inactivos') {
        $query->whereDoesntHave('contratoActual'); // sin contrato
    }
    // "todos" no filtra nada más

    /* ============================
       ORDENAMIENTO  
       ============================ */
    if ($request->orden === 'asc') {

        $query->orderBy('apellidos', 'asc')->orderBy('nombres', 'asc');

    } elseif ($request->orden === 'desc') {

        $query->orderBy('apellidos', 'desc')->orderBy('nombres', 'desc');

    } elseif ($request->orden === 'area') {

        $query->whereHas('contratoActual.area')
              ->join('contratos', 'empleados.id', '=', 'contratos.empleado_id')
              ->join('areas', 'areas.id', '=', 'contratos.area_id')
              ->orderBy('areas.nombre', 'asc')
              ->select('empleados.*');

    } elseif ($request->orden === 'cargo') {

        $query->whereHas('contratoActual.cargo')
              ->join('contratos', 'empleados.id', '=', 'contratos.empleado_id')
              ->join('cargos', 'cargos.id', '=', 'contratos.cargo_id')
              ->orderBy('cargos.cargo', 'asc')
              ->select('empleados.*');
    }

    /* ============================
       EJECUTAR CONSULTA
       ============================ */
    $empleados = $query->get();

    /* ============================
       PARA LOS SELECTS  
       ============================ */
    $cargos = Cargo::orderBy('cargo')->get();

    return view('rrhh.empleados.lista_empleados.index',
        compact('empleados', 'cargos')
    );
}



    public function store(Request $request)
    {
        $request->validate([
            // DATOS PERSONALES
            'nombres'       => 'required|string|max:120',
            'apellidos'     => 'required|string|max:120',

            'dni'           => 'required|digits:8|unique:empleados,dni',
            'correo'        => 'required|email|unique:empleados,correo',

            // VALIDACIÓN DE EDAD (18 – 65)
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fecha = Carbon::parse($value);
                    $edad = $fecha->age;

                    if ($edad < 18) {
                        $fail('El empleado debe tener mínimo 18 años.');
                    }

                    if ($edad > 65) {
                        $fail('El empleado no puede tener más de 65 años.');
                    }

                    if ($fecha->isFuture()) {
                        $fail('La fecha de nacimiento no puede ser futura.');
                    }

                    if ($fecha->year < 1900) {
                        $fail('La fecha de nacimiento es inválida.');
                    }
                }
            ],

            'sexo'              => 'required|in:Masculino,Femenino,Otro',
            'direccion'         => 'nullable|string',
            'estado_civil'      => 'nullable|string',
            'nacionalidad'      => 'nullable|string',
            'telefono'          => 'nullable|string|max:20',

            // CONTACTO DE EMERGENCIA
            'emergencia_nombre'     => 'nullable|string|max:120',
            'emergencia_telefono'   => 'nullable|string|max:20',
            'emergencia_parentesco' => 'nullable|string|max:60',

            // FOTO
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // LABORALES
            'area_id'       => 'required|exists:areas,id',
            'cargo_id'      => 'required|exists:cargos,id',

            'tipo_contrato' => 'required|in:temporal,indefinido',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',

            // PENSIONES
            'sistema_pension' => 'required|in:AFP,ONP',
            'afp_tipo'        => 'nullable|string',

            // PAGO
            'metodo_pago'     => 'required|in:transferencia,efectivo',

            // ADMINISTRATIVOS
            'observaciones'   => 'nullable|string',

            // BENEFICIOS
            'asignacion_familiar' => 'nullable|boolean',
        ]);

        // FOTO
        $foto = $request->hasFile('foto')
            ? $request->file('foto')->store('empleados_fotos', 'public')
            : null;

        // CREAR EMPLEADO
        $empleado = Empleado::create([
            'nombres'       => $request->nombres,
            'apellidos'     => $request->apellidos,
            'dni'           => $request->dni,
            'correo'        => $request->correo,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'          => $request->sexo,
            'direccion'     => $request->direccion,
            'estado_civil'  => $request->estado_civil,
            'nacionalidad'  => $request->nacionalidad,
            'telefono'      => $request->telefono,

            // CONTACTO DE EMERGENCIA
            'contacto_nombre'     => $request->emergencia_nombre,
            'contacto_telefono'   => $request->emergencia_telefono,
            'contacto_parentesco' => $request->emergencia_parentesco,

            'foto'            => $foto,
            'estado'          => 'activo', // SIEMPRE ACTIVO AL REGISTRAR
            'observaciones'   => $request->observaciones,

            'asignacion_familiar' => $request->asignacion_familiar ? 1 : 0,
            'bonificacion'        => 0,
        ]);

        // SUELDO
        $cargo = Cargo::find($request->cargo_id);

        // CREAR CONTRATO (SIEMPRE ACTIVO)
        Contrato::create([
            'empleado_id'   => $empleado->id,
            'area_id'       => $request->area_id,
            'cargo_id'      => $request->cargo_id,

            'tipo_contrato' => $request->tipo_contrato,
            'fecha_inicio'  => $request->fecha_inicio,
            'fecha_fin'     => $request->fecha_fin,

            'sueldo'        => $cargo->sueldo,

            'sistema_pension' => $request->sistema_pension,
            'afp_nombre'      => $request->sistema_pension === 'AFP' ? 'AFP' : null,
            'afp_tipo'        => $request->sistema_pension === 'AFP' ? $request->afp_tipo : null,

            'metodo_pago'     => $request->metodo_pago,
            'banco'           => null,
            'cuenta_bancaria' => null,
            'tipo_cuenta'     => null,

            'estado_contrato' => 'activo'
        ]);

        return redirect()->route('empleados.index')
            ->with('success', 'Empleado y contrato registrados correctamente.');
    }

   public function detalle(Request $request, $id)
{
    $empleado = Empleado::with(['contratos.cargo', 'contratos.area', 'asistencias'])->findOrFail($id);

    $contratoActual = $empleado->contratoActual;
    $vacaciones = 0;
    $ultimoContrato = $empleado->contratos->last();

    if ($contratoActual) {
        $inicio = Carbon::parse($contratoActual->fecha_inicio);
        $vacaciones = $inicio->diffInYears(now()) * 30;
    }

    $mes = $request->mes ?? 'actual';
    $anio = $request->anio ?? Carbon::now()->year;

    return view('rrhh.empleados.detalle', compact('empleado', 'vacaciones','ultimoContrato', 'mes', 'anio'));
}

}
