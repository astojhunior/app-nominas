<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Empleado;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Contrato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    /** Crear admin principal (siempre logueado en todas las pruebas) */
    protected function crearAdminPrincipal()
    {
        return Admin::factory()->create([
            'email' => 'admin@nominaempleados.com',
            'password' => Hash::make('123456')
        ]);
    }

    /** Crear estructura necesaria para asignar usuario */
    protected function crearEmpleadoConContrato()
    {
        $area = Area::factory()->create();
        $cargo = Cargo::factory()->create(['area_id' => $area->id]);
        $empleado = Empleado::factory()->create();

        Contrato::factory()->create([
            'empleado_id' => $empleado->id,
            'area_id' => $area->id,
            'cargo_id' => $cargo->id,
            'estado_contrato' => 'activo'
        ]);

        return $empleado;
    }

    /** ============================================
     * 1. Puede ver la página de seguridad
     * ============================================ */
    public function test_puede_ver_pagina_seguridad()
    {
        $admin = $this->crearAdminPrincipal();

        $this->actingAs($admin, 'admin')
            ->get(route('seguridad.index'))
            ->assertStatus(200)
            ->assertSee('Seguridad del Sistema');
    }

    /** ============================================
     * 2. Crear usuario administrador correctamente
     * ============================================ */
    public function test_puede_crear_usuario_admin()
    {
        $admin = $this->crearAdminPrincipal();
        $empleado = $this->crearEmpleadoConContrato();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('seguridad.crear'), [
                'empleado_id' => $empleado->id,
                'nombre_admin' => 'Supervisor',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'email' => $empleado->correo
        ]);
    }

    /** ============================================
     * 3. No permite crear usuario si ya existe
     * ============================================ */
    public function test_no_permite_crear_usuario_duplicado()
    {
        $admin = $this->crearAdminPrincipal();
        $empleado = $this->crearEmpleadoConContrato();

        // Crear primero
        Admin::factory()->create([
            'email' => $empleado->correo
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('seguridad.crear'), [
                'empleado_id' => $empleado->id,
                'nombre_admin' => 'Duplicado',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]);

        $response->assertSessionHas('error');
    }

    /** ============================================
     * 4. Cambiar contraseña correctamente
     * ============================================ */
    public function test_puede_cambiar_password()
    {
        $admin = $this->crearAdminPrincipal();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('seguridad.password'), [
                'password_actual' => '123456',
                'password_nueva' => 'nueva123',
                'password_nueva_confirmation' => 'nueva123'
            ]);

        $response->assertSessionHas('success');

        $this->assertTrue(
            Hash::check('nueva123', $admin->fresh()->password)
        );
    }

    /** ============================================
     * 5. NO permite cambiar contraseña si la actual es incorrecta
     * ============================================ */
    public function test_no_permite_cambiar_password_con_actual_incorrecta()
    {
        $admin = $this->crearAdminPrincipal();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('seguridad.password'), [
                'password_actual' => 'mala',
                'password_nueva' => 'nueva123',
                'password_nueva_confirmation' => 'nueva123'
            ]);

        $response->assertSessionHas('error');
    }

    /** ============================================
     * 6. Admin principal puede eliminar a otro admin
     * ============================================ */
    public function test_admin_principal_puede_eliminar_usuario()
    {
        $adminPrincipal = $this->crearAdminPrincipal();

        $otroAdmin = Admin::factory()->create([
            'password' => Hash::make('123456')
        ]);

        $response = $this->actingAs($adminPrincipal, 'admin')
            ->delete(route('seguridad.eliminar', $otroAdmin->id), [
                'password' => '123456'
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('admins', [
            'id' => $otroAdmin->id
        ]);
    }

    /** ============================================
     * 7. NO permite eliminar si no es admin principal
     * ============================================ */
    public function test_no_permite_eliminar_si_no_es_principal()
    {
        $noPrincipal = Admin::factory()->create([
            'email' => 'otro@correo.com',
            'password' => Hash::make('123456')
        ]);

        $adminObjetivo = Admin::factory()->create();

        $response = $this->actingAs($noPrincipal, 'admin')
            ->delete(route('seguridad.eliminar', $adminObjetivo->id), [
                'password' => '123456'
            ]);

        $response->assertSessionHas('error');
    }

    /** ============================================
     * 8. NO permite eliminarse a sí mismo
     * ============================================ */
    public function test_no_permite_eliminarse_a_si_mismo()
    {
        $adminPrincipal = $this->crearAdminPrincipal();

        $response = $this->actingAs($adminPrincipal, 'admin')
            ->delete(route('seguridad.eliminar', $adminPrincipal->id), [
                'password' => '123456'
            ]);

        $response->assertSessionHas('error');
    }
}
