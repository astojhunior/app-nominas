<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoArchivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'contrato_id',
        'ruta_archivo',
        'tipo_archivo'
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
