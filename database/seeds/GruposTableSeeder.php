<?php

use Illuminate\Database\Seeder;

class GruposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grupos')->insert([
            'id' => 1,
            'descricao' => 'Não Encaminhado',
        ]);

        DB::table('grupos')->insert([
            'id' => 2,
            'descricao' => 'Grupo Teste Azul',
        ]);

        DB::table('grupos')->insert([
            'id' => 3,
            'descricao' => 'Grupo Teste Verde',
        ]);

        DB::table('grupos')->insert([
            'id' => 4,
            'descricao' => 'Grupo Teste Amarelo',
        ]);
    }
}
