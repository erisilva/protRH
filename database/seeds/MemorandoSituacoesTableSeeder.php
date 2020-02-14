<?php

use Illuminate\Database\Seeder;

class MemorandoSituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     
        DB::table('memorando_situacaos')->insert([
            'id' => 1,
            'descricao' => 'Aberto',
        ]);

        DB::table('memorando_situacaos')->insert([
            'id' => 2,
            'descricao' => 'Encaminhado',
        ]);

        DB::table('memorando_situacaos')->insert([
            'id' => 3,
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('memorando_situacaos')->insert([
            'id' => 4,
            'descricao' => 'Concluido',
        ]);
    }
}
