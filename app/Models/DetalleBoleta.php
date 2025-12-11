<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleBoleta extends Model
{
    protected $table = 'detalle_boleta';

    protected $fillable = [
        'boleta_id',
        'tipo',
        'concepto',
        'monto',
        'motivo'
    ];

    public function boleta()
    {
        return $this->belongsTo(Boleta::class);
    }
}
