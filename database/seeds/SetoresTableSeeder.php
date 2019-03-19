<?php

use Illuminate\Database\Seeder;

class SetoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setors')->insert([
            'codigo' => '0001',
            'descricao' => 'Almoxarifado',
        ]);

        DB::table('setors')->insert([
            'codigo' => '0002',
            'descricao' => 'Farmacia',
        ]);

        DB::table('setors')->insert([
            'codigo' => '0003',
            'descricao' => 'Biblioteca',
        ]);

        DB::table('setors')->insert([
            'codigo' => '0004',
            'descricao' => 'Recursos Humanos',
        ]);

        DB::table('setors')->insert([
            'codigo' => '0005',
            'descricao' => 'Informatica',
        ]);

        DB::table('setors')->insert([
            'codigo' => '0006',
            'descricao' => 'Teste',
        ]);
    }
}
