<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'razon_social' => 'Panadería San Jorge',
                'dni_ruc' => '20123456789',
                'nombre_comercial' => 'San Jorge',
                'numero_contacto' => 'Juan Pérez',
                'telefono' => '987654321',
                'estado' => '0'
            ],
            [
                'razon_social' => 'Distribuidora La Moderna',
                'dni_ruc' => '10456789012',
                'nombre_comercial' => 'La Moderna',
                'numero_contacto' => 'María Gómez',
                'telefono' => '912345678',
                'estado' => '0'
            ],
            [
                'razon_social' => 'Molinos del Valle',
                'dni_ruc' => '20567890123',
                'nombre_comercial' => 'Molinos Valle',
                'numero_contacto' => null,
                'telefono' => '956789012',
                'estado' => '0'
            ],
        ]);
    }
}
