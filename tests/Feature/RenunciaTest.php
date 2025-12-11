<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Empleado;
use App\Models\Contrato;
use App\Models\Renuncia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RenunciaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_ver_pagina_de_renuncias()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('renuncias.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function puede_registrar_una_renuncia()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        // empleado con contrato
        $empleado = Empleado::factory()->create([
            'estado' => 'activo'
        ]);

        Contrato::factory()->create([
            'empleado_id' => $empleado->id,
            'estado_contrato' => 'activo',
            'sueldo' => 1200,
        ]);

        $data = [
            'empleado_id'    => $empleado->id,
            'fecha_renuncia' => '2025-01-10',
            'fecha_cese'     => '2025-01-10',
            'motivo'         => 'Motivo de prueba',
        ];

        $response = $this->post(route('renuncias.store'), $data);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('renuncias', [
            'empleado_id' => $empleado->id,
            'estado' => 'registrada',
        ]);

        $this->assertDatabaseHas('empleados', [
            'id'     => $empleado->id,
            'estado' => 'baja',
        ]);
    }

    /** @test */
    public function no_permite_doble_renuncia_activa()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $empleado = Empleado::factory()->create();

        Renuncia::factory()->create([
            'empleado_id' => $empleado->id,
            'estado'      => 'registrada'
        ]);

        $data = [
            'empleado_id'    => $empleado->id,
            'fecha_renuncia' => '2025-01-10',
            'fecha_cese'     => '2025-01-10',
            'motivo'         => 'Intento duplicado',
        ];

        $response = $this->post(route('renuncias.store'), $data);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function puede_anular_renuncia_con_password_correcto()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('12345678')
        ]);

        $this->actingAs($admin, 'admin');

        $empleado = Empleado::factory()->create([
            'estado' => 'baja'
        ]);

        $renuncia = Renuncia::factory()->create([
            'empleado_id' => $empleado->id,
            'estado' => 'registrada'
        ]);

        $response = $this->post(route('renuncias.anular', $renuncia->id), [
            'password' => '12345678',
            'motivo'   => 'Anulación correcta'
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('renuncias', [
            'id'     => $renuncia->id,
            'estado' => 'anulada'
        ]);

        $this->assertDatabaseHas('empleados', [
            'id' => $empleado->id,
            'estado' => 'activo'
        ]);
    }

    /** @test */
    public function no_permite_anular_con_password_incorrecto()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('correcto123')
        ]);

        $this->actingAs($admin, 'admin');

        $empleado = Empleado::factory()->create();

        $renuncia = Renuncia::factory()->create([
            'empleado_id' => $empleado->id,
            'estado' => 'registrada'
        ]);

        $response = $this->post(route('renuncias.anular', $renuncia->id), [
            'password' => 'incorrecto',
            'motivo'   => 'XYZ'
        ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('renuncias', [
            'id'     => $renuncia->id,
            'estado' => 'registrada' // NO debe cambiar
        ]);
    }
}
