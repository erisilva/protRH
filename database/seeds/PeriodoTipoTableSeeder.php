<?php

use Illuminate\Database\Seeder;

class PeriodoTipoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('periodo_tipos')->insert([
            'descricao' => 'Férias Normal',
        ]);

        DB::table('periodo_tipos')->insert([
            'descricao' => 'Férias 1º Parcela',
        ]);

        DB::table('periodo_tipos')->insert([
            'descricao' => 'Férias 2º Parcela',
        ]);

        DB::table('periodo_tipos')->insert([
            'descricao' => 'Licença Medica',
        ]);

        DB::table('periodo_tipos')->insert([
            'descricao' => 'Licença Maternidade',
        ]);

        DB::table('periodo_tipos')->insert([
            'descricao' => 'Não Definido',
        ]);
    }
}
