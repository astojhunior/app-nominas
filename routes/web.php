<?php

use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\SancionController;
use App\Http\Controllers\RenunciaController;
use App\Http\Controllers\BoletaController;
use App\Http\Controllers\BoletaGratificacionController;
use App\Http\Controllers\BoletaCTSController;
use App\Http\Controllers\BoletasBonoController;
use App\Http\Controllers\SeguridadController;
use App\Http\Controllers\ReportesPersonalController;

/*
|--------------------------------------------------------------------------
| LOGIN ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::get('/env-check', function () {
    return [
        'app_key' => config('app.key'),
        'app_env' => config('app.env'),
        'debug' => config('app.debug'),
        'db' => [
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
        ]
    ];
});


Route::get('/',                 [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::get('/admin/login',      [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login',     [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout',    [AdminAuthController::class, 'logout'])->name('admin.logout');


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (require auth:admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ÁREAS Y CARGOS
    |--------------------------------------------------------------------------
    */

    // Vista principal
    Route::get('/rrhh/areas_cargos', [AreaController::class, 'index'])
        ->name('areas_cargos.index');

    // Áreas
    Route::post('/areas/store',            [AreaController::class, 'store'])->name('areas.store');
    Route::post('/areas/check-password',   [AreaController::class, 'checkPassword'])->name('areas.checkPassword');
    Route::post('/areas/update',           [AreaController::class, 'update'])->name('areas.update');
    Route::post('/areas/delete',           [AreaController::class, 'destroy'])->name('areas.destroy');

    // Cargos
    Route::post('/cargos/store',           [CargoController::class, 'store'])->name('cargos.store');
    Route::post('/cargos/check-password',  [CargoController::class, 'checkPassword'])->name('cargos.checkPassword');
    Route::post('/cargos/update',          [CargoController::class, 'update'])->name('cargos.update');
    Route::post('/cargos/delete',          [CargoController::class, 'destroy'])->name('cargos.destroy');


    /*
    |--------------------------------------------------------------------------
    | EMPLEADOS
    |--------------------------------------------------------------------------
    */
    Route::get('/rrhh/empleados',                [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/rrhh/empleados/registrar',      [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/rrhh/empleados/store',         [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/rrhh/empleados/{id}/detalle',   [EmpleadoController::class, 'detalle'])->name('empleados.detalle');


    /*
    |--------------------------------------------------------------------------
    | CONTRATOS
    |--------------------------------------------------------------------------
    */
    Route::get('/rrhh/contratos/{id}',           [ContratoController::class, 'ver'])->name('contratos.ver');
    Route::get('/rrhh/contratos/pdf/{id}',       [ContratoController::class, 'descargarPDF'])->name('contratos.descargarPDF');
    Route::post('/rrhh/contratos/{id}/subir',    [ContratoController::class, 'subirFirmado'])->name('contratos.subirFirmado');


    /*
    |--------------------------------------------------------------------------
    | ASISTENCIAS
    |--------------------------------------------------------------------------
    */

    // Registrar
    Route::get('/asistencias/registrar',         [AsistenciaController::class, 'create'])->name('asistencias.registrar');
    Route::post('/asistencias/registrar',        [AsistenciaController::class, 'store'])->name('asistencias.store');

    // Editar y eliminar
    Route::put('/asistencias/{id}',              [AsistenciaController::class, 'update'])->name('asistencias.update');
    Route::delete('/asistencias/{id}',           [AsistenciaController::class, 'destroy'])->name('asistencias.destroy');

    // Lista
    Route::get('/asistencias',                   [AsistenciaController::class, 'index'])->name('asistencias.index');

    // Exportaciones
    Route::get('/asistencias/export/excel',      [AsistenciaController::class, 'exportExcel'])->name('asistencias.export.excel');
    Route::get('/asistencias/export/pdf',        [AsistenciaController::class, 'exportPdf'])->name('asistencias.export.pdf');


    /*
    |--------------------------------------------------------------------------
    | TURNOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('turnos')->group(function () {
        Route::get('/',     [TurnoController::class, 'index'])->name('turnos.index');
        Route::post('/',    [TurnoController::class, 'store'])->name('turnos.store');
        Route::put('/{id}', [TurnoController::class, 'update'])->name('turnos.update');
        Route::delete('/{id}', [TurnoController::class, 'destroy'])->name('turnos.delete');
    });


    /*
    |--------------------------------------------------------------------------
    | TIPOS DE SANCIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('tiposancion')->group(function () {
        Route::get('/',         [App\Http\Controllers\TipoSancionController::class, 'index'])->name('tiposancion.index');
        Route::post('/',        [App\Http\Controllers\TipoSancionController::class, 'store'])->name('tiposancion.store');
        Route::put('/{id}',     [App\Http\Controllers\TipoSancionController::class, 'update'])->name('tiposancion.update');
        Route::delete('/{id}',  [App\Http\Controllers\TipoSancionController::class, 'destroy'])->name('tiposancion.delete');
    });


    /*
    |--------------------------------------------------------------------------
    | SANCIONES
    |--------------------------------------------------------------------------
    */
    Route::prefix('sanciones')->group(function () {

        Route::get('/',              [SancionController::class, 'index'])->name('sanciones.index');
        Route::post('/store',        [SancionController::class, 'store'])->name('sanciones.store');
        Route::get('/pdf/{id}',      [SancionController::class, 'generarPDF'])->name('sanciones.pdf');

        // Actualizar y anular
        Route::put('/{id}/update', [SancionController::class, 'update'])->name('sanciones.update');
        Route::post('/{id}/anular', [SancionController::class, 'anular'])->name('sanciones.anular');

    });


    /*
    |--------------------------------------------------------------------------
    | RENUNCIAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('renuncias')->group(function () {

        Route::get('/',               [RenunciaController::class, 'index'])->name('renuncias.index');
        Route::post('/store',         [RenunciaController::class, 'store'])->name('renuncias.store');
        Route::post('/{id}/anular',   [RenunciaController::class, 'anular'])->name('renuncias.anular');
        Route::get('/{id}/pdf',       [RenunciaController::class, 'pdf'])->name('renuncias.pdf');
    });


    /*
    |--------------------------------------------------------------------------
    | BOLETAS (FIN DE MES)
    |--------------------------------------------------------------------------
    */
    Route::prefix('boletas')->group(function () {

        Route::get('/', [BoletaController::class, 'index'])->name('boletas.index');

        // Fin de mes
        Route::get('/fin-mes',                [BoletaController::class, 'indexFinMes'])->name('boletas.fin_mes.index');
        Route::post('/fin-mes',               [BoletaController::class, 'finMesStore'])->name('boletas.fin_mes.store');
        Route::post('/fin-mes/confirmar',     [BoletaController::class, 'finMesConfirmar'])->name('boletas.fin_mes.confirmar');
        Route::post('/fin-mes/generar',       [BoletaController::class, 'finMesGenerar'])->name('boletas.fin_mes.generar');
        Route::get('/fin-mes/pdf/{id}',       [BoletaController::class, 'pdf'])->name('boletas.fin_mes.pdf');
        Route::delete('/fin-mes/eliminar/{id}',[BoletaController::class, 'eliminar'])->name('boletas.fin_mes.eliminar');
    });


    /*
    |--------------------------------------------------------------------------
    | BOLETAS GRATIFICACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('boletas/gratificacion')->group(function () {
        Route::get('/',           [BoletaGratificacionController::class, 'index'])->name('boletas.gratificacion.index');
        Route::get('/confirmar',  [BoletaGratificacionController::class, 'confirmar'])->name('boletas.gratificacion.confirmar');
        Route::post('/generar',   [BoletaGratificacionController::class, 'generar'])->name('boletas.gratificacion.generar');
        Route::get('/pdf/{id}',   [BoletaGratificacionController::class, 'pdf'])->name('boletas.gratificacion.pdf');
    });


    /*
    |--------------------------------------------------------------------------
    | BOLETAS CTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('boletas/cts')->group(function () {

        Route::get('/',              [BoletaCTSController::class, 'index'])->name('boletas.cts.index');
        Route::post('/confirmar',    [BoletaCTSController::class, 'confirmar'])->name('boletas.cts.confirmar');
        Route::post('/generar',      [BoletaCTSController::class, 'generar'])->name('boletas.cts.generar');
        Route::get('/pdf/{id}',      [BoletaCTSController::class, 'pdf'])->name('boletas.cts.pdf');
    });


    /*
    |--------------------------------------------------------------------------
    | BOLETAS LIQUIDACIÓN
    |--------------------------------------------------------------------------
    */
    Route::prefix('boletas/liquidacion')->group(function () {

        Route::get('/',             [\App\Http\Controllers\BoletaLiquidacionController::class, 'index'])->name('boletas.liquidacion.index');
        Route::delete('/delete/{id}',[\App\Http\Controllers\BoletaLiquidacionController::class, 'destroy'])->name('boletas.liquidacion.delete');
        Route::get('/confirmar',    [\App\Http\Controllers\BoletaLiquidacionController::class, 'confirmar'])->name('boletas.liquidacion.confirmar');
        Route::post('/generar',     [\App\Http\Controllers\BoletaLiquidacionController::class, 'generar'])->name('boletas.liquidacion.generar');
        Route::get('/pdf/{id}',     [\App\Http\Controllers\BoletaLiquidacionController::class, 'pdf'])->name('boletas.liquidacion.pdf');
    });


    /*
    |--------------------------------------------------------------------------
    | MÓDULO BONOS
    |--------------------------------------------------------------------------
    */

    // Vista principal
    Route::get('/bonos', function () {
        return view('bonos.index');
    })->name('bonos.menu');

    Route::get('/bonos/gestion',        [App\Http\Controllers\BonoController::class, 'index'])->name('bonos.index');
    Route::post('/bonos/store',         [App\Http\Controllers\BonoController::class, 'store'])->name('bonos.store');
    Route::put('/bonos/update/{id}',    [App\Http\Controllers\BonoController::class, 'update'])->name('bonos.update');
    Route::delete('/bonos/delete/{id}', [App\Http\Controllers\BonoController::class, 'destroy'])->name('bonos.delete');
    Route::post('/bonos/renovar/{id}',  [App\Http\Controllers\BonoController::class, 'renovar'])->name('bonos.renovar');

    // Boletas de bonos
    Route::get('/boletas/bonos',                       [BoletasBonoController::class, 'index'])->name('boletas.bonos.index');
    Route::get('/boletas/bonos/{bono}/confirmar',      [BoletasBonoController::class, 'confirmar'])->name('boletas.bonos.confirmar');
    Route::post('/boletas/bonos/{bono}/generar',       [BoletasBonoController::class, 'generar'])->name('boletas.bonos.generar');
    Route::get('/boletas/bonos/pdf/{boleta}',          [BoletasBonoController::class, 'pdf'])->name('boletas.bonos.pdf');
    Route::delete('/boletas/bonos/eliminar/{id}',      [BoletasBonoController::class, 'destroy'])->name('boletas.bonos.destroy');


    /*
    |--------------------------------------------------------------------------
    | SEGURIDAD (gestión de admins)
    |--------------------------------------------------------------------------
    */
    Route::prefix('seguridad')->group(function () {

        Route::get('/',                       [SeguridadController::class, 'index'])->name('seguridad.index');
        Route::post('/crear-usuario',         [SeguridadController::class, 'crearUsuario'])->name('seguridad.crear');
        Route::post('/cambiar-password',      [SeguridadController::class, 'cambiarPassword'])->name('seguridad.password');

        Route::delete('/eliminar/{id}',       [SeguridadController::class, 'eliminarAdmin'])->name('seguridad.eliminar');
    });


    /*
    |--------------------------------------------------------------------------
    | REPORTES PERSONAL
    |--------------------------------------------------------------------------
    */
    Route::prefix('reportes/personal')->group(function () {

        Route::get('/',                     [ReportesPersonalController::class, 'index'])->name('reportes.personal.index');

        Route::get('/activos',              [ReportesPersonalController::class, 'activos'])->name('reportes.personal.activos');
        Route::get('/activos/pdf',          [ReportesPersonalController::class, 'activosPDF'])->name('reportes.personal.activos.pdf');

        Route::get('/cesados',              [ReportesPersonalController::class, 'cesados'])->name('reportes.personal.cesados');
        Route::get('/cesados/pdf',          [ReportesPersonalController::class, 'cesadosPDF'])->name('reportes.personal.cesados.pdf');

        Route::get('/por-area',             [ReportesPersonalController::class, 'porArea'])->name('reportes.personal.area');
        Route::get('/por-area/pdf',         [ReportesPersonalController::class, 'porAreaPDF'])->name('reportes.personal.area.pdf');

        Route::get('/por-cargo',            [ReportesPersonalController::class, 'porCargo'])->name('reportes.personal.cargo');
        Route::get('/por-cargo/pdf',        [ReportesPersonalController::class, 'porCargoPDF'])->name('reportes.personal.cargo.pdf');
    });

}); 
