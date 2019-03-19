<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Role = Perfil, operador pode ter nenhum ou vários perfis
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        DB::table('roles')->insert([
            'name' => 'gerente',
            'description' => 'Gerente',
        ]);

        DB::table('roles')->insert([
            'name' => 'operador',
            'description' => 'Operador',
        ]);

        DB::table('roles')->insert([
            'name' => 'leitor',
            'description' => 'Leitor',
        ]);
    }
}
