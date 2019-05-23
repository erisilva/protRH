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
            'descricao' => 'Aberto',
        ]);

        DB::table('memorando_situacaos')->insert([
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('memorando_situacaos')->insert([
            'descricao' => 'Deferido',
        ]);

        DB::table('memorando_situacaos')->insert([
            'descricao' => 'Concluido',
        ]);

        DB::table('memorando_situacaos')->insert([
            'descricao' => 'Cancelado',
        ]);
    }
}
