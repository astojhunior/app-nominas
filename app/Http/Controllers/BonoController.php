<?php

namespace App\Http\Controllers;

use App\Models\Bono;
use App\Models\Empleado;
use App\Models\Cargo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BonoController extends Controller
{
    /**
     * Mostrar lista de bonos
     */
    public function index()
{
    // Para la lista
    $bonosActivos = Bono::with(['empleado', 'cargo'])
        ->where('estado', 'activo')
        ->orderBy('created_at', 'desc')
        ->get();

    $bonosVencidos = Bono::with(['empleado', 'cargo'])
        ->where('estado', 'vencido')
        ->orderBy('created_at', 'desc')
        ->get();

    // Para el formulario
    $empleados = Empleado::where('estado', 'activo')->get();
    $cargos    = Cargo::all();

    // Meses para renovar
    $meses = [
        1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
        7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
    ];

    return view('boletas.bonos.index', compact(
        'bonosActivos',
        'bonosVencidos',
        'empleados',
        'cargos',
        'meses'
    ));
}


    /**
     * Vista crear bono
     */
    public function create()
    {
        return view('bonos.create', [
            'empleados' => Empleado::where('estado', 'activo')->get(),
            'cargos'    => Cargo::all(),
        ]);
    }

    /**
     * Guardar bono
     */
  public function store(Request $request)
{
    $request->validate([
        'dirigido_a'        => 'required|in:empleado,cargo,todos',
        'empleado_id'       => 'nullable|exists:empleados,id',
        'cargo_id'          => 'nullable|exists:cargos,id',
        'nombre'            => 'required|string|max:255',
        'monto'             => 'required|numeric|min:0',
        'motivo'            => 'nullable|string|max:255',
        'fecha_aplicacion'  => 'required|date',
        'meses_aplicacion'  => 'required|array|min:1',
    ]);

    if ($request->dirigido_a === 'empleado') {
        $request->merge(['cargo_id' => null]);
    } elseif ($request->dirigido_a === 'cargo') {
        $request->merge(['empleado_id' => null]);
    } else {
        $request->merge([
            'empleado_id' => null,
            'cargo_id'    => null,
        ]);
    }

    Bono::create([
        'dirigido_a'        => $request->dirigido_a,
        'empleado_id'       => $request->empleado_id,
        'cargo_id'          => $request->cargo_id,
        'nombre'            => $request->nombre,
        'monto'             => $request->monto,
        'motivo'            => $request->motivo,
        'fecha_aplicacion'  => $request->fecha_aplicacion,
        'meses_aplicacion'  => $request->meses_aplicacion,
        'estado'            => 'activo',
    ]);

    return redirect()
        ->route('bonos.index', ['tab' => 'lista'])
        ->with('success', 'Bono registrado correctamente.');
}
    /**
     * Vista editar bono
     */
    public function edit($id)
    {
        return view('bonos.edit', [
            'bono'      => Bono::findOrFail($id),
            'empleados' => Empleado::where('estado', 'activo')->get(),
            'cargos'    => Cargo::all(),
        ]);
    }

    /**
     * Actualizar bono
     */
public function update(Request $request, $id)
{
    // Validación básica SOLO de los campos que realmente editas
   $request->validate([
    'nombre'    => 'required',
    'monto'     => 'required|numeric',
    'motivo'    => 'required',
    'password'  => 'required'
], [
    'password.required' => 'Debe ingresar su contraseña para confirmar.',
]);

    // Validar contraseña del administrador logueado
    if (!Hash::check($request->password, Auth::guard('admin')->user()->password)) {
        return back()
            ->withErrors(['edit' => 'Contraseña incorrecta.'])
            ->withInput()
            ->with('edit_id', $id);
    }

    // Buscar bono
    $bono = Bono::findOrFail($id);

    // Actualizar SOLO los campos permitidos
    $bono->update([
        'nombre' => $request->nombre,
        'monto'  => $request->monto,
        'motivo' => $request->motivo,
    ]);

    // Redirigir con éxito
    return redirect()
        ->route('bonos.index', ['tab' => 'lista'])
        ->with('success', 'Bono actualizado correctamente.');
}




    /**
     * Eliminar bono con contraseña (tabla admin)
     */
   public function destroy(Request $request, $id)
{
    $bono = Bono::findOrFail($id);

    if (!Hash::check($request->password, Auth::guard('admin')->user()->password)) {
        return back()->with('error', 'Contraseña incorrecta.');
    }

    $bono->delete();

    return redirect()
        ->route('bonos.index', ['tab' => 'lista'])
        ->with('success', 'Bono eliminado correctamente.');
}


    /**
     * Actualiza automáticamente el estado del bono según los meses aplicados
     */
    public static function actualizarEstados()
    {
        $hoy = Carbon::now()->month;

        $bonos = Bono::all();

        foreach ($bonos as $bono) {

            $meses = collect($bono->meses_aplicacion)->map(fn($m) => intval($m));

            if ($meses->contains($hoy)) {
                $bono->update(['estado' => 'activo']);
            } else {
                $bono->update(['estado' => 'vencido']);
            }
        }
    }

    /**
     * Renovar bono
     */
    public function renovar(Request $request, $id)
{
    $request->validate([
        'meses_aplicacion' => 'required|array|min:1',
    ]);

    $bono = Bono::findOrFail($id);

    $bono->update([
        'meses_aplicacion' => $request->meses_aplicacion,
        'estado'           => 'activo',
    ]);

    return redirect()
        ->route('bonos.index', ['tab' => 'lista'])
        ->with('success', 'Bono renovado exitosamente.');
}

}
