<?php

use Illuminate\Database\Seeder;

class SolicitacaoSituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('solicitacao_situacaos')->insert([
            'id' => 1,
            'descricao' => 'Aberto',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'id' => 2,
            'descricao' => 'Encaminhado',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'id' => 3,
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'id' => 4,
            'descricao' => 'Concluido',
        ]);
    }
}
