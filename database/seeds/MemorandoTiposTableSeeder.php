<?php

use Illuminate\Database\Seeder;

class MemorandoTiposTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('memorando_tipos')->insert([
            'descricao' => 'Décimo Terceiro',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'Licença Saúde',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'Licença Maternidade',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'Pedido de 1/3 de Férias',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'Pedido de Transferência',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'Requerimento de Férias',
        ]);

        DB::table('memorando_tipos')->insert([
            'descricao' => 'RH Interno',
        ]);
    }
}
