<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoArchivo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ContratoController extends Controller
{
    // Descargar contrato en PDF
    public function descargarPDF($id)
    {
        $contrato = Contrato::with(['empleado', 'cargo', 'area'])->findOrFail($id);

        $pdf = PDF::loadView('rrhh.contratos.plantilla_pdf', compact('contrato'))
                  ->setPaper('A4', 'portrait');

        return $pdf->download('Contrato_'.$contrato->empleado->dni.'.pdf');
    }

    // Ver contrato + archivos + subir firmado
    public function ver($id)
    {
        $contrato = Contrato::with(['empleado', 'cargo', 'area', 'archivos'])->findOrFail($id);
        return view('rrhh.contratos.ver', compact('contrato'));
    }

    // Subir contrato firmado
    public function subirFirmado(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|mimes:pdf|max:5000'
        ]);

        $ruta = $request->file('archivo')->store('contratos_firmados', 'public');

        ContratoArchivo::create([
            'contrato_id'  => $id,
            'ruta_archivo' => $ruta,
            'tipo_archivo' => 'contrato_firmado'
        ]);

        return back()->with('success', 'Contrato firmado subido correctamente.');
    }
}
