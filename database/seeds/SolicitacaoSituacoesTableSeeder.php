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
            'descricao' => 'Aberto',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'descricao' => 'Deferido',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'descricao' => 'Concluido',
        ]);

        DB::table('solicitacao_situacaos')->insert([
            'descricao' => 'Cancelado',
        ]);
    }
}
