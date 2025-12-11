<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'nombre'   => 'Administrador Principal',
            'email'    => 'admin@nominaempleados.com',
            'password' => Hash::make('admin123'), // puedes cambiar la clave
        ]);
    }
}
