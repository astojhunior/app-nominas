<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CargoController extends Controller
{
    /* ============================================================
       REGISTRAR CARGO
    ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'area_id'     => 'required|exists:areas,id',
            'cargo'       => 'required|string|max:120',
            'sueldo'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Cargo::create([
            'area_id'     => $request->area_id,
            'cargo'       => trim($request->cargo),
            'sueldo'      => $request->sueldo,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->back()->with('success_cargo_created', 'Cargo registrado exitosamente.');
    }



    /* ============================================================
       VALIDAR CONTRASEÑA PARA EDITAR
    ============================================================ */
    public function checkPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'cargo_id' => 'required|exists:cargos,id',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta.'
            ]);
        }

        return response()->json(['success' => true]);
    }



    /* ============================================================
       ACTUALIZAR CARGO
    ============================================================ */
    public function update(Request $request)
    {
        $request->validate([
            'cargo_id'    => 'required|exists:cargos,id',
            'area_id'     => 'required|exists:areas,id',
            'cargo'       => 'required|string|max:120',
            'sueldo'      => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $cargo = Cargo::find($request->cargo_id);

        if (!$cargo) {
            return redirect()->back()->with('error_cargo_update', 'Cargo no encontrado.');
        }

        $cargo->update([
            'area_id'     => $request->area_id,
            'cargo'       => trim($request->cargo),
            'sueldo'      => $request->sueldo,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->back()->with('success_cargo_updated', 'Cargo actualizado correctamente.');
    }



    /* ============================================================
       ELIMINAR CARGO
    ============================================================ */
    public function destroy(Request $request)
    {
        $request->validate([
            'cargo_id' => 'required|exists:cargos,id',
            'password' => 'required|string',
        ]);

        // Validar contraseña
        $admin = Auth::guard('admin')->user();
        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error_cargo_delete', 'Contraseña incorrecta.');
        }

        $cargo = Cargo::find($request->cargo_id);

        if (!$cargo) {
            return redirect()->back()->with('error_cargo_delete', 'Cargo no encontrado.');
        }

        // Validación futura para módulo empleados
        // if ($cargo->empleados()->exists()) {
        //     return redirect()->back()->with('error_cargo_delete', 'No se puede eliminar: hay empleados asociados.');
        // }

        $cargo->delete();

        return redirect()->back()->with('success_cargo_deleted', 'Cargo eliminado exitosamente.');
    }
}
