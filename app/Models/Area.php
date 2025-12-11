<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    /* ============================================
       CONFIGURACIÓN DE ATRIBUTOS
    ============================================ */

    protected $table = 'areas';

    protected $fillable = [
        'nombre',
    ];

    protected $casts = [
        'nombre' => 'string',
    ];

    /* ============================================
       RELACIONES
    ============================================ */

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

    // Relación preparada para tus empleados (cuando lo crees)
    // public function empleados()
    // {
    //     return $this->hasMany(Empleado::class);
    // }

    /* ============================================
       ACCESORS Y MUTATORS
    ============================================ */

    // Guarda el nombre en formato capitalizado.
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucfirst(strtolower(trim($value)));
    }

    public function contratos()
{
    return $this->hasMany(\App\Models\Contrato::class);
}

public function areaActual()
{
    return $this->hasOneThrough(
        Area::class,
        Contrato::class,
        'empleado_id', // FK en contratos
        'id',          // FK en áreas
        'id',          // Empleado actual
        'area_id'      // Area_id en contrato
    )->where('estado_contrato', 'activo')
     ->latest('contratos.fecha_inicio');
}
}
