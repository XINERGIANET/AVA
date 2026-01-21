<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'chochi',
            'email' => 'chochi',
            'password' => Hash::make('chochi'),
            'rol_id' => 1, // ID del rol Admin
            'activo' => true,
        ]);
    }
}
