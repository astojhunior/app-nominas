<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\AsistenciaController;

class AsistenciaModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Forzar BD SQLite en memoria
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->crearTablas();
    }

    /**
     * Crear tablas mínimas necesarias
     */
    private function crearTablas()
    {
        Schema::dropAllTables();

        // AREAS
        Schema::create('areas', function (Blueprint $t) {
            $t->increments('id');
            $t->string('nombre');
            $t->timestamps();
        });

        // CARGOS
        Schema::create('cargos', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('area_id');
            $t->string('cargo');
            $t->decimal('sueldo', 8, 2);
            $t->timestamps();
        });

        // TURNOS
        Schema::create('turnos', function (Blueprint $t) {
            $t->increments('id');
            $t->string('nombre');
            $t->string('hora_ingreso');
            $t->string('hora_salida');
            $t->timestamps();
        });

        // EMPLEADOS
        Schema::create('empleados', function (Blueprint $t) {
            $t->increments('id');
            $t->string('nombres');
            $t->string('apellidos');
            $t->string('dni');
            $t->string('correo');
            $t->string('estado')->default('activo');
            $t->timestamps();
        });

        // CONTRATOS
        Schema::create('contratos', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('empleado_id');
            $t->integer('area_id');
            $t->integer('cargo_id');
            $t->integer('turno_id')->nullable();
            $t->string('tipo_contrato');
            $t->date('fecha_inicio');
            $t->string('estado_contrato')->default('activo');
            $t->decimal('sueldo', 8, 2)->default(0);
            $t->string('sistema_pension')->nullable();
            $t->string('metodo_pago')->nullable();
            $t->timestamps();
        });

        // ASISTENCIAS
        Schema::create('asistencias', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('empleado_id');
            $t->integer('contrato_id');
            $t->integer('turno_id')->nullable();
            $t->date('fecha');
            $t->string('hora_entrada')->nullable();
            $t->string('hora_salida')->nullable();
            $t->string('break_inicio')->nullable();
            $t->string('break_fin')->nullable();
            $t->integer('tardanza_inicio_turno')->default(0);
            $t->integer('tardanza_break')->default(0);
            $t->integer('tardanza_total')->default(0);
            $t->string('estado_asistencia');
            $t->string('tipo_jornada')->nullable();
            $t->string('observaciones')->nullable();
            $t->integer('horas_extra')->default(0);
            $t->string('justificacion')->nullable();
            $t->integer('marcado_manual')->default(1);
            $t->string('origen_marcado')->nullable();
            $t->timestamps();
        });
    }

    /**
     * PRUEBA 1 — No permite registrar sin fecha
     */
    public function test_no_permite_registrar_sin_fecha()
    {
        $req = Request::create('/asistencias', 'POST', [
            'fecha' => null,
            'asistencias' => []
        ]);

        $resp = (new AsistenciaController())->store($req);

        $this->assertTrue(session()->has('error'));
    }

    /**
     * PRUEBA 2 — No permite registrar sin datos de asistencias
     */
    public function test_no_permite_registrar_sin_datos()
    {
        $req = Request::create('/asistencias', 'POST', [
            'fecha' => '2025-12-10',
            'asistencias' => []
        ]);

        $resp = (new AsistenciaController())->store($req);

        $this->assertTrue(session()->has('error'));
    }

    /**
     * PRUEBA 3 — Evita registrar asistencia duplicada
     */
    public function test_evita_asistencia_duplicada()
    {
        // Crear estructuras base
        \DB::table('areas')->insert(['nombre' => 'RRHH']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Admin', 'sueldo' => 1500]);
        \DB::table('turnos')->insert(['nombre' => 'Mañana', 'hora_ingreso' => '07:00', 'hora_salida' => '15:00']);

        // Crear empleado y contrato
        \DB::table('empleados')->insert([
            'id' => 1,
            'nombres' => 'Juan',
            'apellidos' => 'Chocano',
            'dni' => '12345678',
            'correo' => 'j@test.com',
            'estado' => 'activo'
        ]);

        \DB::table('contratos')->insert([
            'id' => 1,
            'empleado_id' => 1,
            'area_id' => 1,
            'cargo_id' => 1,
            'turno_id' => 1,
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => '2024-01-01',
            'estado_contrato' => 'activo'
        ]);

        // Primera asistencia
        \DB::table('asistencias')->insert([
            'empleado_id' => 1,
            'contrato_id' => 1,
            'turno_id' => 1,
            'fecha' => '2025-12-10',
            'estado_asistencia' => 'turno'
        ]);

        // Intento duplicado
        $req = Request::create('/asistencias', 'POST', [
            'fecha' => '2025-12-10',
            'asistencias' => [
                1 => [
                    'estado_asistencia' => 'turno',
                    'turno_id' => 1,
                    'hora_entrada' => '07:10',
                    'hora_salida' => '15:00',
                    'break_inicio' => '10:00',
                    'break_fin' => '10:50'
                ]
            ]
        ]);

        (new AsistenciaController())->store($req);

        $count = \DB::table('asistencias')->where('empleado_id', 1)->count();

        $this->assertEquals(1, $count);
    }

    /**
     * PRUEBA 4 — Registro exitoso con tardanza calculada
     */
    public function test_registro_exitoso_con_tardanza()
    {
        \DB::table('areas')->insert(['nombre' => 'RRHH']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Admin', 'sueldo' => 1500]);
        \DB::table('turnos')->insert(['id' => 1, 'nombre' => 'Mañana', 'hora_ingreso' => '07:00', 'hora_salida' => '15:00']);

        \DB::table('empleados')->insert([
            'id' => 1,
            'nombres' => 'Carlos',
            'apellidos' => 'Ramos',
            'dni' => '55667788',
            'correo' => 'carlos@test.com',
            'estado' => 'activo'
        ]);

        \DB::table('contratos')->insert([
            'id' => 1,
            'empleado_id' => 1,
            'area_id' => 1,
            'cargo_id' => 1,
            'turno_id' => 1,
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => '2024-01-01',
            'estado_contrato' => 'activo'
        ]);

        $req = Request::create('/asistencias', 'POST', [
            'fecha' => '2025-12-10',
            'asistencias' => [
                1 => [
                    'estado_asistencia' => 'turno',
                    'turno_id' => 1,
                    'hora_entrada' => '07:10',
                    'hora_salida' => '15:00',
                    'break_inicio' => '10:00',
                    'break_fin' => '10:55'
                ]
            ]
        ]);

        (new AsistenciaController())->store($req);

        $asistencia = \DB::table('asistencias')->first();

        $this->assertNotNull($asistencia);
        $this->assertEquals(10, $asistencia->tardanza_inicio_turno);
        $this->assertEquals(10, $asistencia->tardanza_break);
        $this->assertEquals(20, $asistencia->tardanza_total);
    }
}
