<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'correo',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'estado_civil',
        'nacionalidad',
        'telefono',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_parentesco',
        'foto',
        'estado',
        'observaciones',
        'fecha_cese',
        'asignacion_familiar',
        'bonificacion'
    ];

    protected $casts = [
        'fecha_nacimiento'   => 'date',
        'asignacion_familiar'=> 'boolean',
        'bonificacion'       => 'decimal:2'
    ];

    // TODOS los contratos
    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }

    // CONTRATO ACTUAL (SIN latestOfMany)
    public function contratoActual()
{
    return $this->hasOne(Contrato::class)
        ->where('estado_contrato', 'activo')
        ->latestOfMany('fecha_inicio');
}

    // ÁREA actual del contrato activo
    public function areaActual()
    {
        return $this->hasOneThrough(
            Area::class,
            Contrato::class,
            'empleado_id',
            'id',
            'id',
            'area_id'
        )
        ->where('estado_contrato', 'activo')
        ->latest('contratos.fecha_inicio');
    }

    // CARGO actual
    public function cargoActual()
    {
        return optional($this->contratoActual)->cargo;
    }

    // TURNO actual
    public function turnoActual()
    {
        return optional($this->contratoActual)->turno;
    }

    // Asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function renuncias()
    {
        return $this->hasMany(Renuncia::class, 'empleado_id');
    }

    // ACCESOR: área actual
public function getAreaActualAttribute()
{
    return optional($this->contratoActual)->area;
}

// ACCESOR: cargo actual
public function getCargoActualAttribute()
{
    return optional($this->contratoActual)->cargo;
}
public function sanciones() { return $this->hasMany(Sancion::class); }
public function boletas() { return $this->hasMany(Boleta::class); }
public function contrato()
{
    return $this->hasOne(Contrato::class, 'empleado_id')
                ->latestOfMany('fecha_inicio');
}
public function renuncia()
{
    return $this->hasOne(Renuncia::class, 'empleado_id')->latestOfMany();
}
}
