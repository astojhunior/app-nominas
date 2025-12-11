<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Turno;

class TurnoController extends Controller
{
    /**
     * Mostrar lista de turnos
     */
    public function index()
    {
        $turnos = Turno::orderBy('nombre')->get();
        return view('turnos.index', compact('turnos'));
    }

    /**
     * Registrar un nuevo turno
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:50',
            'hora_ingreso'  => 'required|date_format:H:i',
            'hora_salida'   => 'required|date_format:H:i',
        ]);

        // YA NO BLOQUEA TURNOS QUE CRUZAN MEDIANOCHE

        Turno::create([
            'nombre'        => $request->nombre,
            'hora_ingreso'  => $request->hora_ingreso,
            'hora_salida'   => $request->hora_salida,
        ]);

        return back()->with('success', 'Turno registrado correctamente.');
    }

    /**
     * Actualizar un turno
     */
    public function update(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);

        $request->validate([
            'nombre'        => 'required|string|max:50',
            'hora_ingreso'  => 'required|date_format:H:i',
            'hora_salida'   => 'required|date_format:H:i',
        ]);

        //  TAMPOCO BLOQUEA TURNOS NOCTURNOS

        $turno->update([
            'nombre'        => $request->nombre,
            'hora_ingreso'  => $request->hora_ingreso,
            'hora_salida'   => $request->hora_salida,
        ]);

        return back()->with('success', 'Turno actualizado correctamente.');
    }

    /**
     * Eliminar turno
     */
    public function destroy(Request $request, $id)
{
    //  validar campo contraseña
    $request->validate([
        'password' => 'required'
    ]);

    $admin = auth('admin')->user();

    // verificar contraseña ingresada
    if (!Hash::check($request->password, $admin->password)) {
        return back()->with('error', 'Contraseña incorrecta. No se pudo eliminar.');
    }

    // eliminar turno
    $turno = Turno::findOrFail($id);
    $turno->delete();

    return back()->with('success', 'Turno eliminado correctamente.');
}
}
