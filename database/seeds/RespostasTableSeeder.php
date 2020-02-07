<?php

use Illuminate\Database\Seeder;

class RespostasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('respostas')->insert([
            'id' => '1',
            'descricao' => 'Ainda não disponível',
        ]);
        DB::table('respostas')->insert([
            'id' => '2',
            'descricao' => 'Deferido',
        ]);

        DB::table('respostas')->insert([
            'id' => '3',
            'descricao' => 'Indeferido',
        ]);
    }
}
