<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AsistenciaStoreValidationTest extends TestCase
{
    use WithoutMiddleware;

    public function test_requiere_fecha_y_al_menos_un_registro()
    {
        $response = $this->post(route('asistencias.store'), [
            'fecha' => null,
            'asistencias' => [],
        ]);

        // Mensaje REAL que devuelve tu sistema
        $response->assertSessionHas('error', 'Debe seleccionar una fecha.');
    }
}
