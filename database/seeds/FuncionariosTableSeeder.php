<?php

use Illuminate\Database\Seeder;

class FuncionariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('funcionarios')->insert([
            'nome' => 'Erivelton da Silva',
            'matricula' => '159023',
            'email' => 'erisilva@erisilva.net',
            'numeropasta' => '1',
        ]);

        DB::table('funcionarios')->insert([
            'nome' => 'Abadia dos Santos',
            'matricula' => '182698',
            'email' => 'abadia@erisilva.net',
            'numeropasta' => '2',
        ]);

        DB::table('funcionarios')->insert([
            'nome' => 'Jorge do Couto Maia',
            'matricula' => '8277387',
            'email' => 'jorgecoutinho@erisilva.net',
            'numeropasta' => '3',
        ]);

        DB::table('funcionarios')->insert([
            'nome' => 'maria Aparecida da Silva',
            'matricula' => '172671',
            'email' => 'mariaaparecidade@erisilva.net',
            'numeropasta' => '4',
        ]);

        DB::table('funcionarios')->insert([
            'nome' => 'André Maia Carvalho',
            'matricula' => '8273829',
            'email' => 'andremaia@erisilva.net',
            'numeropasta' => '5',
        ]);

        DB::table('funcionarios')->insert([
            'nome' => 'João Paulo Brandão',
            'matricula' => '159023',
            'email' => 'joaopaulo@erisilva.net',
            'numeropasta' => '6',
        ]);
    }
}
