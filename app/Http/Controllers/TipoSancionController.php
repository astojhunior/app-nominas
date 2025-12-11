<?php

namespace App\Http\Controllers;

use App\Models\TipoSancion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TipoSancionController extends Controller
{
    /**
     * Mostrar lista + formulario
     */
    public function index()
    {
        $tipos = TipoSancion::orderBy('nombre')->get();
        return view('sanciones.tipos.index', compact('tipos'));
    }

    /**
     * Registrar nuevo tipo de sanción
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100|unique:tipos_sancion,nombre',
            'descripcion'   => 'nullable|string|max:255',
            'requiere_dias' => 'required|boolean',
        ]);

        TipoSancion::create([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'requiere_dias' => $request->requiere_dias,
            'estado'        => true,
        ]);

        return back()->with('success', 'Tipo de sanción registrado correctamente.');
    }

    /**
     * Actualizar tipo de sanción
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoSancion::findOrFail($id);

        $request->validate([
            'nombre'        => "required|string|max:100|unique:tipos_sancion,nombre,{$id}",
            'descripcion'   => 'nullable|string|max:255',
            'requiere_dias' => 'required|boolean',
        ]);

        $tipo->update([
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'requiere_dias' => $request->requiere_dias,
        ]);

        return back()->with('success', 'Tipo de sanción actualizado correctamente.');
    }

    /**
     * Eliminar con verificación de contraseña
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->password, $admin->password)) {
            return back()->with('error', 'La contraseña ingresada es incorrecta.');
        }

        $tipo = TipoSancion::findOrFail($id);

        // ✅ No permitir eliminar si tiene sanciones vinculadas
        if ($tipo->sanciones()->exists()) {
            return back()->with('error', 'No se puede eliminar porque tiene sanciones registradas.');
        }

        $tipo->delete();

        return back()->with('success', 'Tipo de sanción eliminado correctamente.');
    }
}
