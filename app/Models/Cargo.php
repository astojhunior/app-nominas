<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    /* ============================================
       CONFIGURACIÓN DE ATRIBUTOS
    ============================================ */

    protected $table = 'cargos';

    protected $fillable = [
        'area_id',
        'cargo',
        'sueldo',
        'descripcion',
    ];

    protected $casts = [
        'area_id'     => 'integer',
        'cargo'       => 'string',
        'sueldo'      => 'decimal:2',
        'descripcion' => 'string',
    ];

    /* ============================================
       RELACIONES
    ============================================ */

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
public function contratos()
{
    return $this->hasMany(\App\Models\Contrato::class);
}

    public function setCargoAttribute($value)
    {
        $this->attributes['cargo'] = ucfirst(strtolower(trim($value)));
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = trim($value);
    }
}
