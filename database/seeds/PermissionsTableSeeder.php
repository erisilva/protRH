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
            'description' => 'Registrar novo tipo de protocolo',
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

        //Situação do protocolo
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.index',
            'description' => 'Lista de Situações do protocolos',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.create',
            'description' => 'Registrar nova situação do protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.edit',
            'description' => 'Alterar dados da situação do protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.delete',
            'description' => 'Excluir situação do protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.show',
            'description' => 'Mostrar dados da situação do protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolosituacao.export',
            'description' => 'Exportação de dados das situações dos protocolos',
        ]);

        //Tipo de período
        DB::table('permissions')->insert([
            'name' => 'periodotipo.index',
            'description' => 'Lista de tipos de período',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodotipo.create',
            'description' => 'Registrar novo tipo de período',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodotipo.edit',
            'description' => 'Alterar dados do tipo de período',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodotipo.delete',
            'description' => 'Excluir tipo de período',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodotipo.show',
            'description' => 'Mostrar dados do tipo de período',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodotipo.export',
            'description' => 'Exportação de dados dos tipos de período',
        ]);

        //Permissões do protocolo
        DB::table('permissions')->insert([
            'name' => 'protocolo.index',
            'description' => 'Lista de protocolos',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.create',
            'description' => 'Registrar novo protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.edit',
            'description' => 'Alterar dados de um protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.delete',
            'description' => 'Excluir protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.show',
            'description' => 'Mostrar dados de um protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.export',
            'description' => 'Exportação de dados dos protocolos',
        ]);

        //Permissões para periodos do protocolos
        DB::table('permissions')->insert([
            'name' => 'periodo.create',
            'description' => 'Registrar novo período no protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'periodo.delete',
            'description' => 'Excluir período do protocolo',
        ]);

        //Permissões para tramitações dos protocolos
        DB::table('permissions')->insert([
            'name' => 'tramitacao.create',
            'description' => 'Registrar nova tramitação no protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'tramitacao.delete',
            'description' => 'Excluir tramitação do protocolo',
        ]);

        //Tipo de memorando
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.index',
            'description' => 'Lista de tipos de memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.create',
            'description' => 'Registrar novo tipo de memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.edit',
            'description' => 'Alterar dados do tipo de memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.delete',
            'description' => 'Excluir tipo de memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.show',
            'description' => 'Mostrar dados do tipo de memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandotipo.export',
            'description' => 'Exportação de dados dos tipos de memorando',
        ]);

        //Situação do memorando
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.index',
            'description' => 'Lista de situações do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.create',
            'description' => 'Registrar nova situação do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.edit',
            'description' => 'Alterar dados da situação do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.delete',
            'description' => 'Excluir situação do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.show',
            'description' => 'Mostrar dados do situação do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorandosituacao.export',
            'description' => 'Exportação de dados das situações do memorando',
        ]);

        //Memorandos
        DB::table('permissions')->insert([
            'name' => 'memorando.index',
            'description' => 'Lista de memorandos',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.create',
            'description' => 'Registrar novo memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.edit',
            'description' => 'Alterar dados do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.delete',
            'description' => 'Excluir memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.show',
            'description' => 'Mostrar dados do memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.export',
            'description' => 'Exportação de dados dos memorandos',
        ]);

        //Permissões para tramitações dos protocolos
        DB::table('permissions')->insert([
            'name' => 'memorando.tramitacao.create',
            'description' => 'Registrar nova tramitação no memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.tramitacao.delete',
            'description' => 'Excluir tramitação do memorando',
        ]);

        //Tipo de Ofício
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.index',
            'description' => 'Lista de tipos de ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.create',
            'description' => 'Registrar novo tipo de ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.edit',
            'description' => 'Alterar dados do tipo de ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.delete',
            'description' => 'Excluir tipo de ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.show',
            'description' => 'Mostrar dados do tipo de ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiotipo.export',
            'description' => 'Exportação de dados dos tipos de ofício',
        ]);

        //Situação do memorando
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.index',
            'description' => 'Lista de situações do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.create',
            'description' => 'Registrar nova situação do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.edit',
            'description' => 'Alterar dados da situação do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.delete',
            'description' => 'Excluir situação do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.show',
            'description' => 'Mostrar dados do situação do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficiosituacao.export',
            'description' => 'Exportação de dados das situações do ofício',
        ]);

        //Ofícios
        DB::table('permissions')->insert([
            'name' => 'oficio.index',
            'description' => 'Lista de ofícios',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.create',
            'description' => 'Registrar novo ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.edit',
            'description' => 'Alterar dados do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.delete',
            'description' => 'Excluir ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.show',
            'description' => 'Mostrar dados do ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.export',
            'description' => 'Exportação de dados dos ofícios',
        ]);

        //Permissões para tramitações dos ofícios
        DB::table('permissions')->insert([
            'name' => 'oficio.tramitacao.create',
            'description' => 'Registrar nova tramitação no ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.tramitacao.delete',
            'description' => 'Excluir tramitação do ofício',
        ]);        


    }
}
