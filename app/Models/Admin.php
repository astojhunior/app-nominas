<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    // Nombre de la tabla (por si acaso)
    protected $table = 'admins';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'nombre',
        'email',
        'password',
    ];

    // Ocultar estos campos cuando se convierta a array/json
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
