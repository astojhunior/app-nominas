<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSancion extends Model
{
    protected $table = 'tipos_sancion';

    protected $fillable = [
        'nombre',
        'requiere_dias',
        'descripcion',
        'estado',
    ];

    /**
     * Un tipo de sanción puede tener muchas sanciones
     */
    public function sanciones()
    {
        return $this->hasMany(Sancion::class, 'tipo_sancion_id');
    }
}
