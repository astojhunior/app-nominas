<?php

namespace App\Http\Controllers;

use App\Models\Renuncia;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class RenunciaController extends Controller
{
    public function index(Request $request)
    {
        $empleados = Empleado::where('estado', 'activo')
            ->orderBy('apellidos')
            ->get();

        $renuncias = Renuncia::with(['empleado.contratoActual.cargo'])
            ->orderByDesc('fecha_cese')
            ->get();

        $meses = Renuncia::selectRaw('YEAR(fecha_cese) as anio, MONTH(fecha_cese) as mes')
            ->groupBy('anio','mes')
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->get();

        return view('renuncias.index', compact(
            'empleados',
            'renuncias',
            'meses'
        ));
    }

   public function store(Request $request)
{
    $request->validate([
        'empleado_id'    => 'required|exists:empleados,id',
        'fecha_renuncia' => 'required|date',
        'fecha_cese'     => 'required|date|before_or_equal:fecha_renuncia',
        'motivo'         => 'required|string|min:3',
    ]);

    if (Renuncia::where('empleado_id', $request->empleado_id)
        ->where('estado', 'registrada')
        ->exists()) {
        return back()->with('error', 'El empleado ya tiene una renuncia activa.');
    }

    Renuncia::create([
        'empleado_id'    => $request->empleado_id,
        'fecha_renuncia' => $request->fecha_renuncia,
        'fecha_cese'     => $request->fecha_cese,
        'motivo'         => $request->motivo,
        'estado'         => 'registrada',
    ]);

    $empleado = Empleado::find($request->empleado_id);
    $empleado->update([
        'estado' => 'baja',
        'fecha_cese' => $request->fecha_cese
    ]);

    if ($empleado->contratoActual) {
        $empleado->contratoActual->update([
            'fecha_fin' => $request->fecha_cese,
            'estado_contrato' => 'rescindido'
        ]);
    }

    return redirect()
        ->route('renuncias.index', ['ver' => 'lista'])
        ->with('success', 'Renuncia registrada correctamente.');
}


    public function anular(Request $request, $id)
    {
        $request->validate([
            'password' => 'required',
            'motivo'   => 'required|string|max:255',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }

        $r = Renuncia::findOrFail($id);
        $r->update([
            'estado' => 'anulada',
            'motivo' => $request->motivo,
        ]);

        $empleado = $r->empleado;
        $empleado->update([
            'estado' => 'activo',
            'fecha_cese' => null
        ]);

        if ($empleado->contratoActual) {
            $empleado->contratoActual->update([
                'fecha_fin' => null,
                'estado_contrato' => 'activo'
            ]);
        }

        return back()->with('success', 'Renuncia anulada y empleado reactivado.');
    }
    
public function pdf($id)
{
    $renuncia = Renuncia::with(['empleado', 'empleado.contratoActual.cargo'])
        ->findOrFail($id);

    $pdf = PDF::loadView('renuncias.pdf', [
        'renuncia' => $renuncia
    ])->setPaper('A4');

    $nombre = strtolower(
        str_replace(' ', '_',
            $renuncia->empleado->nombres . ' ' .
            $renuncia->empleado->apellidos
        )
    );

    $filename = "{$nombre}_renuncia_{$renuncia->fecha_renuncia}.pdf";

    return $pdf->download($filename);
}
}
