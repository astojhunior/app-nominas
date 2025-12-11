<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boleta extends Model
{
    use HasFactory;
    protected $fillable = [
        'empleado_id',
        'tipo',
        'periodo_mes',
        'periodo_anio',

        // Nuevo: datos completos
        'fecha_ingreso',
        'fecha_cese',
        'antiguedad_texto',
        'dias_trabajados_reales',

        'sueldo',
        'asignacion',
        'base',

        'vac_meses',
        'vac_dias',
        'monto_vacaciones',

        'cts_meses',
        'cts_dias',
        'monto_cts',

        'grati_meses',
        'monto_grati',

        'dias_ultimo_mes',
        'monto_dias_mes',

        'total_liquidacion',

        // Campos contables
        'total_ingresos',
        'total_descuentos',
        'total_aportes',
        'neto_pagar',

        'estado',
        'fecha_generacion',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
    ];


    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    // Relación con los detalles
    public function detalles()
    {
        return $this->hasMany(DetalleBoleta::class);
    }
}
