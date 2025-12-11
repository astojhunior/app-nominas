<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\RenunciaController;

class RenunciaValidationTest extends TestCase
{
    /** @test */
    public function no_permite_registrar_renuncia_sin_motivo()
    {
        // Simulamos un request sin campo "motivo"
        $request = Request::create('/renuncias', 'POST', [
            'empleado_id' => 1,
            'fecha_renuncia' => '2025-12-01',
            'motivo' => '' // vacío → debe fallar
        ]);

        $controller = new RenunciaController();

        // Capturar la excepción de validación
        try {
            $controller->store($request);
            $this->fail('La validación no se ejecutó correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('motivo', $e->errors());
        }
    }
}
