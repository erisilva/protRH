<?php

use Illuminate\Database\Seeder;

class OficioSituacoesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oficio_situacaos')->insert([
            'descricao' => 'Aberto',
        ]);

        DB::table('oficio_situacaos')->insert([
            'descricao' => 'Em Tramitação',
        ]);

        DB::table('oficio_situacaos')->insert([
            'descricao' => 'Deferido',
        ]);

        DB::table('oficio_situacaos')->insert([
            'descricao' => 'Concluido',
        ]);

        DB::table('oficio_situacaos')->insert([
            'descricao' => 'Cancelado',
        ]);
    }
}
