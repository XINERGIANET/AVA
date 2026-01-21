<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('products')->insert([
            [
                'nombre' => 'Diesel DB5',
                'marca' => 'Estandar',
                'tipo' => 'Producto',
                'categoria_interna' => 'Combustible',
                'unidad_medida' => 'Galon',
                'precio' => 14.70,
            ],
            [
                'nombre' => 'Gasolina Regular',
                'marca' => 'Estandar',
                'tipo' => 'Producto',
                'categoria_interna' => 'Combustible',
                'unidad_medida' => 'Galon',
                'precio' => 15.70,
            ],
            [
                'nombre' => 'Gasolina Premium',
                'marca' => 'Estandar',
                'tipo' => 'Producto',
                'categoria_interna' => 'Combustible',
                'unidad_medida' => 'Galon',
                'precio' => 15.70,
            ],
        ]);
    }
}
