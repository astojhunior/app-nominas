<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Admin;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SeguridadController extends Controller
{
    public function index()
    {
        // Empleados con área/cargo reales
       $empleados = Empleado::with(['contratoActual.cargo', 'contratoActual.area'])
    ->get()
    ->map(function ($e) {

        return [
            'id'      => $e->id,
            'nombre'  => trim($e->apellidos . ' ' . $e->nombres),
            'correo'  => $e->correo,

            // Área
            'area'    => $e->areaActual?->nombre ?? 'Sin área',
            'area_id' => $e->areaActual?->id,

            // Cargo
            'cargo'   => $e->cargoActual?->cargo ?? 'Sin cargo',
            'cargo_id'=> $e->cargoActual?->id,
        ];
    });

        $areas  = Area::all(['id', 'nombre']);
        $cargos = Cargo::all(['id', 'cargo', 'area_id']);
        $adminActual = Auth::guard('admin')->user();
        $admins = Admin::all();

        return view('seguridad.index', [
            'empleados'   => $empleados,
            'areas'       => $areas,
            'cargos'      => $cargos,
            'adminActual' => $adminActual,
            'admins'      => $admins,
        ]);
    }

    public function crearUsuario(Request $request)
    {
        $request->validate([
            'empleado_id'  => 'required|exists:empleados,id',
            'nombre_admin' => 'required|string|max:255',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        $empleado = Empleado::findOrFail($request->empleado_id);

        // ERROR SIMULADO: se cambia la columna email por correo_admin (no existe)
if (Admin::where('email', $empleado->correo)->exists()) {
    return back()->with('error', 'Este empleado ya tiene usuario.')
                 ->with('tab', 'asignar');
}

        $nombreCompleto = trim($empleado->apellidos . ' ' . $empleado->nombres);
        $nombreFinal = $nombreCompleto . " - " . $request->nombre_admin;

        Admin::create([
            'nombre'   => $nombreFinal,
            'email'    => $empleado->correo,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Usuario creado correctamente.')
                     ->with('tab', 'asignar');
    }

    public function cambiarPassword(Request $request)
{
    $request->validate([
        'password_actual' => 'required',
        'password_nueva'  => 'required|min:6|confirmed',
    ]);

    $admin = Auth::guard('admin')->user();

    // Validar contraseña actual
    if (!Hash::check($request->password_actual, $admin->password)) {
        return back()->with('error', 'Contraseña actual incorrecta.')
                     ->with('tab', 'password');
    }

    // Actualizar contraseña usando update() que evita errores de fillable
    Admin::where('id', $admin->id)->update([
        'password' => Hash::make($request->password_nueva),
    ]);

    return back()->with('success', 'Contraseña actualizada.')
                 ->with('tab', 'password');
}

    public function eliminarAdmin(Request $request, $id)
{
    $request->validate([
        'password' => 'required',
    ]);

    $admin = Admin::findOrFail($id);
    $actual = Auth::guard('admin')->user();

    // No eliminarse a sí mismo
    if ($admin->id == $actual->id) {
        return back()->with('error', 'No puedes eliminar tu propio usuario.');
    }

    // Solo el admin principal puede eliminar
    if ($actual->email !== 'admin@nominaempleados.com') {
        return back()->with('error', 'Solo el administrador principal puede eliminar usuarios.');
    }

    // Verificar contraseña
    if (!Hash::check($request->password, $actual->password)) {
        return back()->with('error', 'La contraseña ingresada es incorrecta.');
    }

    // Eliminar
    $admin->delete();

    return back()->with('success', 'Usuario administrador eliminado correctamente.');
}




}
