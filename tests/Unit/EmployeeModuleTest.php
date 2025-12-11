<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\EmpleadoController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class EmployeeModuleTest extends TestCase
{
    use DatabaseTransactions; // <-- FUNDAMENTAL para evitar usar la BD real

    protected function setUp(): void
    {
        parent::setUp();

        // Forzar a Laravel a usar sqlite en memoria aunque phpunit use mysql
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->prepararTablas();
    }

    /**
     * Crear tablas mínimas necesarias.
     */
   private function prepararTablas()
{
    // -------- BORRAR TODAS LAS TABLAS SI EXISTEN --------
    $tables = \DB::select("SELECT name FROM sqlite_master WHERE type='table'");

    foreach ($tables as $table) {
        if ($table->name !== 'sqlite_sequence') {
            Schema::drop($table->name);
        }
    }

    // -------- CREAR TABLAS --------

    Schema::create('areas', function (Blueprint $table) {
        $table->increments('id');
        $table->string('nombre');
        $table->timestamps();
    });

    Schema::create('cargos', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('area_id');
        $table->string('cargo');
        $table->decimal('sueldo', 8, 2);
        $table->string('descripcion')->nullable();
        $table->timestamps();
    });

    Schema::create('empleados', function (Blueprint $table) {
        $table->increments('id');
        $table->string('nombres');
        $table->string('apellidos');
        $table->string('dni')->unique();
        $table->string('correo')->unique();
        $table->date('fecha_nacimiento');
        $table->string('sexo');
        $table->string('direccion')->nullable();
        $table->string('estado_civil')->nullable();
        $table->string('nacionalidad')->nullable();
        $table->string('telefono')->nullable();
        $table->string('contacto_nombre')->nullable();
        $table->string('contacto_telefono')->nullable();
        $table->string('contacto_parentesco')->nullable();
        $table->string('foto')->nullable();
        $table->string('estado')->default('activo');
        $table->string('observaciones')->nullable();
        $table->boolean('asignacion_familiar')->default(0);
        $table->decimal('bonificacion', 8, 2)->default(0); // <-- IMPORTANTE
        $table->timestamps();
    });

    Schema::create('contratos', function (Blueprint $table) {
    $table->increments('id');
    $table->integer('empleado_id');
    $table->integer('area_id');
    $table->integer('cargo_id');

    $table->string('tipo_contrato');
    $table->date('fecha_inicio');
    $table->date('fecha_fin')->nullable();
    $table->decimal('sueldo', 8, 2);

    $table->string('sistema_pension');
    $table->string('afp_nombre')->nullable();
    $table->string('afp_tipo')->nullable();

    // CAMPOS QUE FALTABAN
    $table->string('metodo_pago');
    $table->string('banco')->nullable();
    $table->string('cuenta_bancaria')->nullable();
    $table->string('tipo_cuenta')->nullable();

    $table->string('estado_contrato')->default('activo');
    $table->timestamps();
});

}


    /** PRUEBA 1 — No permite registrar sin nombres */
    public function test_no_permite_registrar_sin_nombres()
    {
        \DB::table('areas')->insert(['nombre' => 'RRHH']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Asistente', 'sueldo' => 1200]);

        $this->expectException(ValidationException::class);

        $req = Request::create('/empleados', 'POST', [
            'nombres' => '',
            'apellidos' => 'Perez',
            'dni' => '12345678',
            'correo' => 'correo@test.com',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'Masculino',
            'area_id' => 1,
            'cargo_id' => 1,
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => '2024-01-01',
            'sistema_pension' => 'AFP',
            'metodo_pago' => 'efectivo'
        ]);

        (new EmpleadoController())->store($req);
    }

    /** PRUEBA 2 — DNI inválido */
    public function test_no_permite_dni_invalido()
    {
        \DB::table('areas')->insert(['nombre' => 'Adm']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Supervisor', 'sueldo' => 1800]);

        $this->expectException(ValidationException::class);

        $req = Request::create('/empleados', 'POST', [
            'nombres' => 'Juan',
            'apellidos' => 'Lopez',
            'dni' => '123', 
            'correo' => 'otro@test.com',
            'fecha_nacimiento' => '1995-01-01',
            'sexo' => 'Masculino',
            'area_id' => 1,
            'cargo_id' => 1,
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => '2024-01-01',
            'sistema_pension' => 'AFP',
            'metodo_pago' => 'efectivo'
        ]);

        (new EmpleadoController())->store($req);
    }

    /** PRUEBA 3 — No permite menor de edad */
    public function test_no_permite_menor_de_edad()
    {
        \DB::table('areas')->insert(['nombre' => 'Producción']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Operario', 'sueldo' => 1500]);

        $fecha_menor = Carbon::now()->subYears(16)->format('Y-m-d');

        $this->expectException(ValidationException::class);

        $req = Request::create('/empleados', 'POST', [
            'nombres' => 'Karen',
            'apellidos' => 'Diaz',
            'dni' => '87654321',
            'correo' => 'karen@test.com',
            'fecha_nacimiento' => $fecha_menor,
            'sexo' => 'Femenino',
            'area_id' => 1,
            'cargo_id' => 1,
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => '2024-01-01',
            'sistema_pension' => 'AFP',
            'metodo_pago' => 'efectivo'
        ]);

        (new EmpleadoController())->store($req);
    }

    /** PRUEBA 4 — Registro exitoso */
    public function test_registro_exitoso_empleado()
    {
        \DB::table('areas')->insert(['nombre' => 'Ventas']);
        \DB::table('cargos')->insert(['area_id' => 1, 'cargo' => 'Vendedor', 'sueldo' => 2000]);

        $req = Request::create('/empleados', 'POST', [
            'nombres' => 'Carlos',
            'apellidos' => 'Ramos',
            'dni' => '55667788',
            'correo' => 'carlos@test.com',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'Masculino',
            'area_id' => 1,
            'cargo_id' => 1,
            'tipo_contrato' => 'temporal',
            'fecha_inicio' => '2024-01-01',
            'sistema_pension' => 'AFP',
            'metodo_pago' => 'transferencia'
        ]);

        (new EmpleadoController())->store($req);

        $empleado = \DB::table('empleados')->where('dni', '55667788')->first();

        $this->assertNotNull($empleado);
        $this->assertEquals('Carlos', $empleado->nombres);
    }
}
