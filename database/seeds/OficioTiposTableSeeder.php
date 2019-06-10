<?php

use Illuminate\Database\Seeder;

class OficioTiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oficio_tipos')->insert([
            'descricao' => 'Décimo Terceiro',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'Licença Saúde',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'Licença Maternidade',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'Pedido de 1/3 de Férias',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'Pedido de Transferência',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'Requerimento de Férias',
        ]);

        DB::table('oficio_tipos')->insert([
            'descricao' => 'RH Interno',
        ]);
    }
}
