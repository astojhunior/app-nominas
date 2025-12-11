<?php

namespace App\Http\Controllers;

use App\Models\Bono;
use App\Models\Empleado;
use App\Models\Boleta;
use App\Models\DetalleBoleta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BoletasBonoController extends Controller
{
    public function index()
    {
        $bonos = Bono::orderBy('fecha_aplicacion', 'desc')->get();

        return view('boletas.bonos.indexboleta', compact('bonos'));
    }


    public function confirmar(Bono $bono)
    {
        // Obtener empleados según tipo de bono
        if ($bono->dirigido_a === 'todos') {

            $empleados = Empleado::where('estado', 'activo')->get();

        } elseif ($bono->dirigido_a === 'cargo') {

            $empleados = Empleado::whereHas('contrato', function ($q) use ($bono) {
                $q->where('cargo_id', $bono->cargo_id)
                  ->where('estado_contrato', 'activo');
            })->get();

        } else { // individual

            $empleados = Empleado::where('id', $bono->empleado_id)->get();
        }

        return view('boletas.bonos.confirmar', compact('bono', 'empleados'));
    }


    public function generar(Request $request, Bono $bono)
    {
        // Obtener empleados según tipo
        if ($bono->dirigido_a === 'todos') {

            $empleados = Empleado::where('estado', 'activo')->get();

        } elseif ($bono->dirigido_a === 'cargo') {

            $empleados = Empleado::whereHas('contrato', function ($q) use ($bono) {
                $q->where('cargo_id', $bono->cargo_id)
                  ->where('estado_contrato', 'activo');
            })->get();

        } else {
            $empleados = Empleado::where('id', $bono->empleado_id)->get();
        }


        foreach ($empleados as $empleado) {

            // Crear boleta de bono
            $boleta = Boleta::create([
                'empleado_id'    => $empleado->id,
                'tipo'           => 'bono',
                'periodo_mes'    => Carbon::parse($bono->fecha_aplicacion)->month,
                'periodo_anio'   => Carbon::parse($bono->fecha_aplicacion)->year,
                'total_ingresos' => $bono->monto,
                'neto_pagar'     => $bono->monto,
                'estado'         => 'generado',
            ]);

            // Detalle
            DetalleBoleta::create([
                'boleta_id' => $boleta->id,
                'tipo'      => 'ingreso',
                'concepto'  => $bono->nombre,
                'monto'     => $bono->monto,
                'motivo'    => $bono->motivo,
            ]);
        }

        return redirect()
            ->route('boletas.bonos.index')
            ->with('success', 'Las boletas por bono fueron generadas exitosamente.');
    }


    public function pdf(Boleta $boleta)
    {
        $empleado = $boleta->empleado;
        $detalles = $boleta->detalles;

        $pdf = PDF::loadView('boletas.bonos.pdf', [
            'boleta'   => $boleta,
            'empleado' => $empleado,
            'detalles' => $detalles
        ])->setPaper('A4', 'portrait');

        // Nombre del archivo PDF
        $filename =
            $empleado->apellidos . '-' .
            $empleado->nombres . '-' .
            $detalles->first()->concepto . '.pdf';

        // Abrir en nueva pestaña (NO descarga)
        return $pdf->stream($filename, false);
    }
    public function destroy($id)
{
    $boleta = Boleta::findOrFail($id);

    // borrar detalles primero
    $boleta->detalles()->delete();

    // borrar boleta
    $boleta->delete();

    return back()->with('success', 'Boleta eliminada correctamente.');
}
}
