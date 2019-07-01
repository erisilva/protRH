<?php

use Illuminate\Database\Seeder;

class SolicitacaoTiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Décimo Terceiro',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Licença Saúde',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Licença Maternidade',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Pedido de 1/3 de Férias',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Pedido de Transferência',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'Requerimento de Férias',
        ]);

        DB::table('solicitacao_tipos')->insert([
            'descricao' => 'RH Interno',
        ]);
    }
}
