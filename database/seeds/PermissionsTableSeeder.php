<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Permission = permissão
     * essas permições só devem ser configuradas pelo administrador
     * as permissões ficam vinculadas a cada método do controlador
     *
     * @return void
     */
    public function run()
    {
    	// permissões possíveis para o cadastro de operadores do sistema
    	// user = operador
        DB::table('permissions')->insert([
            'name' => 'user.index',
            'description' => 'Lista de operadores',
        ]);
        DB::table('permissions')->insert([
            'name' => 'user.create',
            'description' => 'Registrar novo operador',
        ]);
        DB::table('permissions')->insert([
            'name' => 'user.edit',
            'description' => 'Alterar dados do operador',
        ]);
        DB::table('permissions')->insert([
            'name' => 'user.delete',
            'description' => 'Excluir operador',
        ]);
        DB::table('permissions')->insert([
            'name' => 'user.show',
            'description' => 'Mostrar dados do operador',
        ]);
        DB::table('permissions')->insert([
            'name' => 'user.export',
            'description' => 'Exportação de dados dos operadores',
        ]);


		// permissões possíveis para o cadastro de perfis do sistema
        //role = perfil
        DB::table('permissions')->insert([
            'name' => 'role.index',
            'description' => 'Lista de perfis',
        ]);
        DB::table('permissions')->insert([
            'name' => 'role.create',
            'description' => 'Registrar novo perfil',
        ]);
        DB::table('permissions')->insert([
            'name' => 'role.edit',
            'description' => 'Alterar dados do perfil',
        ]);
        DB::table('permissions')->insert([
            'name' => 'role.delete',
            'description' => 'Excluir perfil',
        ]);
        DB::table('permissions')->insert([
            'name' => 'role.show',
            'description' => 'Alterar dados do perfil',
        ]);
        DB::table('permissions')->insert([
            'name' => 'role.export',
            'description' => 'Exportação de dados dos perfis',
        ]);

        // permissões possíveis para o cadastro de permissões do sistema
        //permission = permissão de acesso
        DB::table('permissions')->insert([
            'name' => 'permission.index',
            'description' => 'Lista de permissões',
        ]);
        DB::table('permissions')->insert([
            'name' => 'permission.create',
            'description' => 'Registrar nova permissão',
        ]);
        DB::table('permissions')->insert([
            'name' => 'permission.edit',
            'description' => 'Alterar dados da permissão',
        ]);
        DB::table('permissions')->insert([
            'name' => 'permission.delete',
            'description' => 'Excluir permissão',
        ]);
        DB::table('permissions')->insert([
            'name' => 'permission.show',
            'description' => 'Mostrar dados da permissão',
        ]);
        DB::table('permissions')->insert([
            'name' => 'permission.export',
            'description' => 'Exportação de dados das permissões',
        ]);

        //Funcionarios
        DB::table('permissions')->insert([
            'name' => 'funcionario.index',
            'description' => 'Lista de funcionários',
        ]);
        DB::table('permissions')->insert([
            'name' => 'funcionario.create',
            'description' => 'Registrar novo funcionário',
        ]);
        DB::table('permissions')->insert([
            'name' => 'funcionario.edit',
            'description' => 'Alterar dados do funcionário',
        ]);
        DB::table('permissions')->insert([
            'name' => 'funcionario.delete',
            'description' => 'Excluir funcionário',
        ]);
        DB::table('permissions')->insert([
            'name' => 'funcionario.show',
            'description' => 'Mostrar dados do funcionário',
        ]);
        DB::table('permissions')->insert([
            'name' => 'funcionario.export',
            'description' => 'Exportação de dados dos funcionários',
        ]);


        //Setores
        DB::table('permissions')->insert([
            'name' => 'setor.index',
            'description' => 'Lista de setores',
        ]);
        DB::table('permissions')->insert([
            'name' => 'setor.create',
            'description' => 'Registrar novo setor',
        ]);
        DB::table('permissions')->insert([
            'name' => 'setor.edit',
            'description' => 'Alterar dados do setor',
        ]);
        DB::table('permissions')->insert([
            'name' => 'setor.delete',
            'description' => 'Excluir setor',
        ]);
        DB::table('permissions')->insert([
            'name' => 'setor.show',
            'description' => 'Mostrar dados do setor',
        ]);
        DB::table('permissions')->insert([
            'name' => 'setor.export',
            'description' => 'Exportação de dados dos setores',
        ]);


        //Tipos de protocolo
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.index',
            'description' => 'Lista de tipos de protocolos',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.create',
            'description' => 'Registrar novo protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.edit',
            'description' => 'Alterar dados do tipo de protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.delete',
            'description' => 'Excluir tipo de protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.show',
            'description' => 'Mostrar dados do tipo de protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolotipo.export',
            'description' => 'Exportação de dados dos tipos de protocolos',
        ]);

    }
}
