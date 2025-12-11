<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    /* ============================================================
       VISTA PRINCIPAL DE RRHH
    ============================================================ */
    public function index()
    {
        $areas = Area::orderBy('nombre')->get();
        $cargos = Cargo::with('area')->orderBy('cargo')->get();

        return view('rrhh.areas_cargos.index', compact('areas', 'cargos'));
    }



    /* ============================================================
       REGISTRAR ÁREA
    ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120'
        ]);

        Area::create([
            'nombre' => trim($request->nombre),
        ]);

        return redirect()->back()->with('success_area_created', 'Área registrada correctamente.');
    }



    /* ============================================================
       VALIDAR CONTRASEÑA PARA EDITAR ÁREA
    ============================================================ */
    public function checkPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'area_id'  => 'required|exists:areas,id',
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
       ACTUALIZAR ÁREA
    ============================================================ */
    public function update(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'nombre'  => 'required|string|max:120'
        ]);

        $area = Area::find($request->area_id);

        if (!$area) {
            return redirect()->back()->with('error_area_update', 'Área no encontrada.');
        }

        $area->update([
            'nombre' => trim($request->nombre)
        ]);

        return redirect()->back()->with('success_area_updated', 'Área actualizada correctamente.');
    }



    /* ============================================================
       ELIMINAR ÁREA
    ============================================================ */
    public function destroy(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'password' => 'required|string'
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error_area_delete', 'Contraseña incorrecta.');
        }

        $area = Area::find($request->area_id);

        if (!$area) {
            return redirect()->back()->with('error_area_delete', 'Área no encontrada.');
        }

        // Validar si tiene cargos
        if ($area->cargos()->exists()) {
            return redirect()->back()->with('error_area_delete', 'No se puede eliminar: existen cargos asociados.');
        }

        $area->delete();

        return redirect()->back()->with('success_area_deleted', 'Área eliminada correctamente.');
    }
}
