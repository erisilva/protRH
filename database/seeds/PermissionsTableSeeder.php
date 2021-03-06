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
        DB::table('permissions')->insert([
            'name' => 'protocolo.encaminhar',
            'description' => 'Fazer o encaminhamento do protocolo a grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.concluir',
            'description' => 'Concluir ou finalizar um protocolo',
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

        //Permissões para anexos dos protocolos
        DB::table('permissions')->insert([
            'name' => 'protocolo.anexo.create',
            'description' => 'Salvar um arquivo em anexo no protocolo',
        ]);
        DB::table('permissions')->insert([
            'name' => 'protocolo.anexo.delete',
            'description' => 'Excluir um arquivo em anexo no protocolo',
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
        DB::table('permissions')->insert([
            'name' => 'memorando.encaminhar',
            'description' => 'Fazer o encaminhamento do memorando a grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.concluir',
            'description' => 'Concluir ou finalizar um memorando',
        ]);

        //Permissões para tramitações dos memorandos
        DB::table('permissions')->insert([
            'name' => 'memorando.tramitacao.create',
            'description' => 'Registrar nova tramitação no memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.tramitacao.delete',
            'description' => 'Excluir tramitação do memorando',
        ]);

        //Permissões para anexos dos memorandos
        DB::table('permissions')->insert([
            'name' => 'memorando.anexo.create',
            'description' => 'Salvar um arquivo em anexo no memorando',
        ]);
        DB::table('permissions')->insert([
            'name' => 'memorando.anexo.delete',
            'description' => 'Excluir um arquivo em anexo no memorando',
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

        //Situação do oficio
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
        DB::table('permissions')->insert([
            'name' => 'oficio.encaminhar',
            'description' => 'Fazer o encaminhamento do ofício a grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.concluir',
            'description' => 'Concluir ou finalizar um ofício',
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

        //Permissões para anexos dos oficios
        DB::table('permissions')->insert([
            'name' => 'oficio.anexo.create',
            'description' => 'Salvar um arquivo em anexo no ofício',
        ]);
        DB::table('permissions')->insert([
            'name' => 'oficio.anexo.delete',
            'description' => 'Excluir um arquivo em anexo no ofício',
        ]);       

        //Tipo de Solicitação
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.index',
            'description' => 'Lista de tipos de solicitações',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.create',
            'description' => 'Registrar novo tipo de solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.edit',
            'description' => 'Alterar dados do tipo de solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.delete',
            'description' => 'Excluir tipo de solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.show',
            'description' => 'Mostrar dados do tipo de solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaotipo.export',
            'description' => 'Exportação de dados dos tipos de solicitação',
        ]);

        //Situação da solicitacao
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.index',
            'description' => 'Lista de situações da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.create',
            'description' => 'Registrar nova situação da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.edit',
            'description' => 'Alterar dados da situação da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.delete',
            'description' => 'Excluir situação da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.show',
            'description' => 'Mostrar dados do situação da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacaosituacao.export',
            'description' => 'Exportação de dados das situações da solicitação',
        ]);

        //solicitacao
        DB::table('permissions')->insert([
            'name' => 'solicitacao.index',
            'description' => 'Lista de situações',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.create',
            'description' => 'Registrar nova solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.edit',
            'description' => 'Alterar dados da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.delete',
            'description' => 'Excluir solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.show',
            'description' => 'Mostrar dados da solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.export',
            'description' => 'Exportação de dados das solicitações',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.encaminhar',
            'description' => 'Fazer o encaminhamento da solicitação a grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.concluir',
            'description' => 'Concluir ou finalizar uma solicitação',
        ]);

        //Permissões para tramitações das solicitações
        DB::table('permissions')->insert([
            'name' => 'solicitacao.tramitacao.create',
            'description' => 'Registrar nova tramitação na Solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.tramitacao.delete',
            'description' => 'Excluir tramitação da Solicitação',
        ]);

        //Permissões para anexos das solicitações
        DB::table('permissions')->insert([
            'name' => 'solicitacao.anexo.create',
            'description' => 'Salvar um arquivo em anexo na Solicitação',
        ]);
        DB::table('permissions')->insert([
            'name' => 'solicitacao.anexo.delete',
            'description' => 'Excluir um arquivo em anexo na solicitação',
        ]);


        // Grupos de Trabalho
        DB::table('permissions')->insert([
            'name' => 'grupo.index',
            'description' => 'Lista de grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'grupo.create',
            'description' => 'Registrar novo grupo de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'grupo.edit',
            'description' => 'Alterar dados de um grupo de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'grupo.delete',
            'description' => 'Excluir grupo de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'grupo.show',
            'description' => 'Mostrar dados dos grupos de trabalho',
        ]);
        DB::table('permissions')->insert([
            'name' => 'grupo.export',
            'description' => 'Exportação de dados dos grupos de trabalho',
        ]);  


        // Respostas
        DB::table('permissions')->insert([
            'name' => 'resposta.index',
            'description' => 'Lista de respostas',
        ]);
        DB::table('permissions')->insert([
            'name' => 'resposta.create',
            'description' => 'Registrar nova resposta',
        ]);
        DB::table('permissions')->insert([
            'name' => 'resposta.edit',
            'description' => 'Alterar dados de uma resposta',
        ]);
        DB::table('permissions')->insert([
            'name' => 'resposta.delete',
            'description' => 'Excluir resposta',
        ]);
        DB::table('permissions')->insert([
            'name' => 'resposta.show',
            'description' => 'Mostrar dados das respostas',
        ]);
        DB::table('permissions')->insert([
            'name' => 'resposta.export',
            'description' => 'Exportação de dados das respostas',
        ]);       
    }
}
