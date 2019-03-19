<?php

use Illuminate\Database\Seeder;

class ProtocoloTiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Décimo Terceiro',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Licença Saúde',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Licença Maternidade',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Memorandos Técnicos',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Outros Memorandos',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Pedido de 1/3 de Férias',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Pedido de Transferência',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'Requerimento de Férias',
        ]);

        DB::table('protocolo_tipos')->insert([
            'descricao' => 'RH Interno',
        ]);
    }
}
