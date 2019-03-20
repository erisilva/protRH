<?php

use Illuminate\Database\Seeder;

class ProtocoloSituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('protocolo_situacaos')->insert([
            'descricao' => 'Aberto',
        ]);

        DB::table('protocolo_situacaos')->insert([
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('protocolo_situacaos')->insert([
            'descricao' => 'Deferido',
        ]);

        DB::table('protocolo_situacaos')->insert([
            'descricao' => 'Concluido',
        ]);

        DB::table('protocolo_situacaos')->insert([
            'descricao' => 'Cancelado',
        ]);
    }
}
