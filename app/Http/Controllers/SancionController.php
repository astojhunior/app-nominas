<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use App\Models\TipoSancion;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SancionController extends Controller
{
    /**
     * LISTAR / FORMULARIO
     */
    public function index(Request $request)
    {
        Sancion::where('estado', 'activo')
        ->whereDate('fecha_fin_suspension', '<', now()->toDateString())
        ->update(['estado' => 'cumplido']);

        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->get();
        $tipos = TipoSancion::where('estado', 1)->get();

        $empleadoSeleccionado = null;

        if ($request->ver === 'registrar' && $request->empleado) {
            $empleadoSeleccionado = Empleado::find($request->empleado);
        }

        //LISTADO CON FILTROS
        $sanciones = Sancion::with(['empleado', 'tipo'])
            ->where('estado', '!=', 'anulado');

        // BUSCAR DNI O NOMBRE
        if ($request->buscar) {
            $sanciones->whereHas('empleado', function($q) use ($request) {
                $q->where('dni', 'like', '%'.$request->buscar.'%')
                  ->orWhere('apellidos', 'like', '%'.$request->buscar.'%')
                  ->orWhere('nombres', 'like', '%'.$request->buscar.'%');
            });
        }

        // FILTRO POR TIPO
        if ($request->tipo) {
            $sanciones->where('tipo_sancion_id', $request->tipo);
        }

        // FILTRO POR CARGO
        if ($request->cargo) {
            $sanciones->whereHas('empleado.contratoActual.cargo', function($q) use ($request) {
                $q->where('cargo', $request->cargo);
            });
        }

        // ORDEN
        if ($request->orden === 'az') {
            $sanciones->orderBy('fecha_aplicacion', 'asc');
        } elseif ($request->orden === 'za') {
            $sanciones->orderBy('fecha_aplicacion', 'desc');
        } else {
            $sanciones->orderByDesc('fecha_aplicacion');
        }

        $sanciones = $sanciones->get();

        // PARA PDFs POR MES
        $meses = Sancion::selectRaw('YEAR(fecha_aplicacion) as anio, MONTH(fecha_aplicacion) as mes')
            ->groupBy('anio','mes')
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        return view('sanciones.index', compact(
            'empleados',
            'tipos',
            'sanciones',
            'empleadoSeleccionado',
            'meses'
        ));
    }

    /**
     * REGISTRAR SANCIÓN
     */
    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'       => 'required|exists:empleados,id',
            'tipo_sancion_id'   => 'required|exists:tipos_sancion,id',
            'fecha_aplicacion'  => 'required|date',
            'dias_suspension'   => 'nullable|integer|min:1',
            'observaciones'     => 'nullable|string|max:255'
        ]);

        // evitar duplicado mismo día activo
        $existe = Sancion::where('empleado_id', $request->empleado_id)
            ->where('fecha_aplicacion', $request->fecha_aplicacion)
            ->where('estado', 'activo')
            ->exists();

        if ($existe) {
            return back()->with('error', 'El empleado ya tiene una sanción registrada en esta fecha.');
        }

        $tipo = TipoSancion::findOrFail($request->tipo_sancion_id);

        // calcular fecha fin
        $fechaFin = null;

        if ($tipo->requiere_dias && $request->dias_suspension) {
            $fechaFin = Carbon::parse($request->fecha_aplicacion)
                ->addDays($request->dias_suspension - 1)
                ->format('Y-m-d');
        }

        $sancion = Sancion::create([
            'empleado_id'              => $request->empleado_id,
            'tipo_sancion_id'          => $request->tipo_sancion_id,
            'fecha_aplicacion'         => $request->fecha_aplicacion,
            'dias_suspension'          => $request->dias_suspension,
            'fecha_inicio_suspension'  => $request->fecha_aplicacion,
            'fecha_fin_suspension'     => $fechaFin,
            'motivo'                   => $request->observaciones,
            'estado'                   => 'activo',
        ]);

        return redirect()
        ->route('sanciones.index', ['ver' => 'lista'])
        ->with('success', 'Sanción registrada correctamente.');
    }

    /**
     * ACTUALIZAR SANCIÓN
     */
 public function update(Request $request, $id)
{
    $s = Sancion::findOrFail($id);

    $request->validate([
        'observaciones'     => 'nullable|string|max:255',
        'fecha_aplicacion'  => 'required|date',
        'dias_suspension'   => 'nullable|integer|min:1'
    ]);

    // Cargar relación tipo. A veces en tests viene null.
    $tipo = $s->tipo()->first();

    // Si por alguna razón no tiene tipo, evitar error.
    $requiereDias = $tipo ? $tipo->requiere_dias : false;

    $fechaFin = ($requiereDias && $request->dias_suspension)
        ? Carbon::parse($request->fecha_aplicacion)
            ->addDays($request->dias_suspension - 1)
            ->format('Y-m-d')
        : null;

    $s->update([
        'motivo'                   => $request->observaciones,
        'fecha_aplicacion'         => $request->fecha_aplicacion,
        'dias_suspension'          => $request->dias_suspension,
        'fecha_inicio_suspension'  => $request->fecha_aplicacion,
        'fecha_fin_suspension'     => $fechaFin,
    ]);

  session()->flash('success', 'Sanción actualizada correctamente.');

return redirect()->route('sanciones.index', ['ver' => 'lista']);
}


    /**
     * ANULAR SANCIÓN
     */
  public function anular(Request $request, $id)
{
    $request->validate([
        'password' => 'required',
    ]);

    // USAR GUARD ADMIN
    if (!Hash::check($request->password, Auth::guard('admin')->user()->password)) {
        return back()->with('error', 'Contraseña incorrecta.');
    }

    $s = Sancion::findOrFail($id);

    if ($s->estado !== 'activo') {
        return back()->with('error', 'La sanción ya está anulada.');
    }

    $s->update([
        'estado' => 'anulado',
    ]);

    return back()->with('success', 'Sanción anulada correctamente.');
}


    /**
     * GENERAR PDF
     */
    public function generarPDF($id)
    {
        $s = Sancion::with(['empleado', 'tipo'])->findOrFail($id);

        $pdf = PDF::loadView('pdf.sancion', compact('s'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('sancion_'.$s->id.'.pdf');
    }
}
