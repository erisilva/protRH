<?php

use Illuminate\Database\Seeder;

use App\User;
use App\Role;
use App\Permission;

use Illuminate\Support\Facades\DB;

class AclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // apaga todas as tabelas de relacionamento
        DB::table('role_user')->delete();
        DB::table('permission_role')->delete();

        // recebe os operadores principais principais do sistema
        // utilizo o termo operador em vez de usuário por esse
        // significar usuário do SUS, ou usuário do plano, em vez de pessoa ou cliente
        $administrador = User::where('email','=','adm@mail.com')->get()->first();
        $gerente = User::where('email','=','gerente@mail.com')->get()->first();
        $operador = User::where('email','=','operador@mail.com')->get()->first();
        $leitor = User::where('email','=','leitor@mail.com')->get()->first();

        // recebi os perfis
        $administrador_perfil = Role::where('name', '=', 'admin')->get()->first();
        $gerente_perfil = Role::where('name', '=', 'gerente')->get()->first();
        $operador_perfil = Role::where('name', '=', 'operador')->get()->first();
        $leitor_perfil = Role::where('name', '=', 'leitor')->get()->first();

        // salva os relacionamentos entre operador e perfil
        $administrador->roles()->attach($administrador_perfil);
        $gerente->roles()->attach($gerente_perfil);
        $operador->roles()->attach($operador_perfil);
        $leitor->roles()->attach($leitor_perfil);

        // recebi as permissoes
        // para operadores
		$user_index = Permission::where('name', '=', 'user.index')->get()->first();       
		$user_create = Permission::where('name', '=', 'user.create')->get()->first();      
		$user_edit = Permission::where('name', '=', 'user.edit')->get()->first();        
		$user_delete = Permission::where('name', '=', 'user.delete')->get()->first();      
		$user_show = Permission::where('name', '=', 'user.show')->get()->first();        
		$user_export = Permission::where('name', '=', 'user.export')->get()->first();      
		// para perfis
		$role_index = Permission::where('name', '=', 'role.index')->get()->first();       
		$role_create = Permission::where('name', '=', 'role.create')->get()->first();      
		$role_edit = Permission::where('name', '=', 'role.edit')->get()->first();        
		$role_delete = Permission::where('name', '=', 'role.delete')->get()->first();      
		$role_show = Permission::where('name', '=', 'role.show')->get()->first();        
		$role_export = Permission::where('name', '=', 'role.export')->get()->first();      
		// para permissões
		$permission_index = Permission::where('name', '=', 'permission.index')->get()->first(); 
		$permission_create = Permission::where('name', '=', 'permission.create')->get()->first();
		$permission_edit = Permission::where('name', '=', 'permission.edit')->get()->first();  
		$permission_delete = Permission::where('name', '=', 'permission.delete')->get()->first();
		$permission_show = Permission::where('name', '=', 'permission.show')->get()->first();  
		$permission_export = Permission::where('name', '=', 'permission.export')->get()->first();
		// para funcionarios
		$funcionario_index = Permission::where('name', '=', 'funcionario.index')->get()->first(); 
		$funcionario_create = Permission::where('name', '=', 'funcionario.create')->get()->first();
		$funcionario_edit = Permission::where('name', '=', 'funcionario.edit')->get()->first();  
		$funcionario_delete = Permission::where('name', '=', 'funcionario.delete')->get()->first();
		$funcionario_show = Permission::where('name', '=', 'funcionario.show')->get()->first();  
		$funcionario_export = Permission::where('name', '=', 'funcionario.export')->get()->first();
		// para setores
		$setor_index = Permission::where('name', '=', 'setor.index')->get()->first(); 
		$setor_create = Permission::where('name', '=', 'setor.create')->get()->first();
		$setor_edit = Permission::where('name', '=', 'setor.edit')->get()->first();  
		$setor_delete = Permission::where('name', '=', 'setor.delete')->get()->first();
		$setor_show = Permission::where('name', '=', 'setor.show')->get()->first();  
		$setor_export = Permission::where('name', '=', 'setor.export')->get()->first();
		// para tipos de protocolos
		$protocolotipo_index = Permission::where('name', '=', 'protocolotipo.index')->get()->first(); 
		$protocolotipo_create = Permission::where('name', '=', 'protocolotipo.create')->get()->first();
		$protocolotipo_edit = Permission::where('name', '=', 'protocolotipo.edit')->get()->first();  
		$protocolotipo_delete = Permission::where('name', '=', 'protocolotipo.delete')->get()->first();
		$protocolotipo_show = Permission::where('name', '=', 'protocolotipo.show')->get()->first();  
		$protocolotipo_export = Permission::where('name', '=', 'protocolotipo.export')->get()->first();
		// para situações de protocolos
		$protocolosituacao_index = Permission::where('name', '=', 'protocolosituacao.index')->get()->first(); 
		$protocolosituacao_create = Permission::where('name', '=', 'protocolosituacao.create')->get()->first();
		$protocolosituacao_edit = Permission::where('name', '=', 'protocolosituacao.edit')->get()->first();  
		$protocolosituacao_delete = Permission::where('name', '=', 'protocolosituacao.delete')->get()->first();
		$protocolosituacao_show = Permission::where('name', '=', 'protocolosituacao.show')->get()->first();  
		$protocolosituacao_export = Permission::where('name', '=', 'protocolosituacao.export')->get()->first();
		// para tipos de periodo
		$periodotipo_index = Permission::where('name', '=', 'periodotipo.index')->get()->first(); 
		$periodotipo_create = Permission::where('name', '=', 'periodotipo.create')->get()->first();
		$periodotipo_edit = Permission::where('name', '=', 'periodotipo.edit')->get()->first();  
		$periodotipo_delete = Permission::where('name', '=', 'periodotipo.delete')->get()->first();
		$periodotipo_show = Permission::where('name', '=', 'periodotipo.show')->get()->first();  
		$periodotipo_export = Permission::where('name', '=', 'periodotipo.export')->get()->first();
		// para protocolos
		$protocolo_index = Permission::where('name', '=', 'protocolo.index')->get()->first(); 
		$protocolo_create = Permission::where('name', '=', 'protocolo.create')->get()->first();
		$protocolo_edit = Permission::where('name', '=', 'protocolo.edit')->get()->first();  
		$protocolo_delete = Permission::where('name', '=', 'protocolo.delete')->get()->first();
		$protocolo_show = Permission::where('name', '=', 'protocolo.show')->get()->first();  
		$protocolo_export = Permission::where('name', '=', 'protocolo.export')->get()->first();
		$protocolo_encaminhar = Permission::where('name', '=', 'protocolo.encaminhar')->get()->first();
		$protocolo_concluir = Permission::where('name', '=', 'protocolo.concluir')->get()->first();
		// para protocolos (periodos)
		$periodo_create = Permission::where('name', '=', 'periodo.create')->get()->first(); 
		$periodo_delete = Permission::where('name', '=', 'periodo.delete')->get()->first();
		// para protocolos (tramitacões)
		$tramitacao_create = Permission::where('name', '=', 'tramitacao.create')->get()->first(); 
		$tramitacao_delete = Permission::where('name', '=', 'tramitacao.delete')->get()->first();
		// para protocolos (anexos)
		$protocolo_anexo_create = Permission::where('name', '=', 'protocolo.anexo.create')->get()->first(); 
		$protocolo_anexo_delete = Permission::where('name', '=', 'protocolo.anexo.delete')->get()->first();
		// para tipos de memorando
		$memorandotipo_index = Permission::where('name', '=', 'memorandotipo.index')->get()->first(); 
		$memorandotipo_create = Permission::where('name', '=', 'memorandotipo.create')->get()->first();
		$memorandotipo_edit = Permission::where('name', '=', 'memorandotipo.edit')->get()->first();  
		$memorandotipo_delete = Permission::where('name', '=', 'memorandotipo.delete')->get()->first();
		$memorandotipo_show = Permission::where('name', '=', 'memorandotipo.show')->get()->first();  
		$memorandotipo_export = Permission::where('name', '=', 'memorandotipo.export')->get()->first();
		// para situações do memorando
		$memorandosituacao_index = Permission::where('name', '=', 'memorandosituacao.index')->get()->first(); 
		$memorandosituacao_create = Permission::where('name', '=', 'memorandosituacao.create')->get()->first();
		$memorandosituacao_edit = Permission::where('name', '=', 'memorandosituacao.edit')->get()->first();  
		$memorandosituacao_delete = Permission::where('name', '=', 'memorandosituacao.delete')->get()->first();
		$memorandosituacao_show = Permission::where('name', '=', 'memorandosituacao.show')->get()->first();  
		$memorandosituacao_export = Permission::where('name', '=', 'memorandosituacao.export')->get()->first();
		// memorando
		$memorando_index = Permission::where('name', '=', 'memorando.index')->get()->first(); 
		$memorando_create = Permission::where('name', '=', 'memorando.create')->get()->first();
		$memorando_edit = Permission::where('name', '=', 'memorando.edit')->get()->first();  
		$memorando_delete = Permission::where('name', '=', 'memorando.delete')->get()->first();
		$memorando_show = Permission::where('name', '=', 'memorando.show')->get()->first();  
		$memorando_export = Permission::where('name', '=', 'memorando.export')->get()->first();
		// memorando (tramitacões)
		$memorando_tramitacao_create = Permission::where('name', '=', 'memorando.tramitacao.create')->get()->first(); 
		$memorando_tramitacao_delete = Permission::where('name', '=', 'memorando.tramitacao.delete')->get()->first();
		// memorando (anexos)
		$memorando_anexo_create = Permission::where('name', '=', 'memorando.anexo.create')->get()->first(); 
		$memorando_anexo_delete = Permission::where('name', '=', 'memorando.anexo.delete')->get()->first();
		// para tipos de ofícios
		$oficiotipo_index = Permission::where('name', '=', 'oficiotipo.index')->get()->first(); 
		$oficiotipo_create = Permission::where('name', '=', 'oficiotipo.create')->get()->first();
		$oficiotipo_edit = Permission::where('name', '=', 'oficiotipo.edit')->get()->first();  
		$oficiotipo_delete = Permission::where('name', '=', 'oficiotipo.delete')->get()->first();
		$oficiotipo_show = Permission::where('name', '=', 'oficiotipo.show')->get()->first();  
		$oficiotipo_export = Permission::where('name', '=', 'oficiotipo.export')->get()->first();
		// para situações do ofício
		$oficiosituacao_index = Permission::where('name', '=', 'oficiosituacao.index')->get()->first(); 
		$oficiosituacao_create = Permission::where('name', '=', 'oficiosituacao.create')->get()->first();
		$oficiosituacao_edit = Permission::where('name', '=', 'oficiosituacao.edit')->get()->first();  
		$oficiosituacao_delete = Permission::where('name', '=', 'oficiosituacao.delete')->get()->first();
		$oficiosituacao_show = Permission::where('name', '=', 'oficiosituacao.show')->get()->first();  
		$oficiosituacao_export = Permission::where('name', '=', 'oficiosituacao.export')->get()->first();
		// para ofício
		$oficio_index = Permission::where('name', '=', 'oficio.index')->get()->first(); 
		$oficio_create = Permission::where('name', '=', 'oficio.create')->get()->first();
		$oficio_edit = Permission::where('name', '=', 'oficio.edit')->get()->first();  
		$oficio_delete = Permission::where('name', '=', 'oficio.delete')->get()->first();
		$oficio_show = Permission::where('name', '=', 'oficio.show')->get()->first();  
		$oficio_export = Permission::where('name', '=', 'oficio.export')->get()->first();
		// ofícios (tramitacões)
		$oficio_tramitacao_create = Permission::where('name', '=', 'oficio.tramitacao.create')->get()->first(); 
		$oficio_tramitacao_delete = Permission::where('name', '=', 'oficio.tramitacao.delete')->get()->first();
		// ofícios (anexos)
		$oficio_anexo_create = Permission::where('name', '=', 'oficio.anexo.create')->get()->first(); 
		$oficio_anexo_delete = Permission::where('name', '=', 'oficio.anexo.delete')->get()->first();
		// para tipos de solicitação
		$solicitacaotipo_index = Permission::where('name', '=', 'solicitacaotipo.index')->get()->first(); 
		$solicitacaotipo_create = Permission::where('name', '=', 'solicitacaotipo.create')->get()->first();
		$solicitacaotipo_edit = Permission::where('name', '=', 'solicitacaotipo.edit')->get()->first();  
		$solicitacaotipo_delete = Permission::where('name', '=', 'solicitacaotipo.delete')->get()->first();
		$solicitacaotipo_show = Permission::where('name', '=', 'solicitacaotipo.show')->get()->first();  
		$solicitacaotipo_export = Permission::where('name', '=', 'solicitacaotipo.export')->get()->first();
		// para situações da solicitação
		$solicitacaosituacao_index = Permission::where('name', '=', 'solicitacaosituacao.index')->get()->first(); 
		$solicitacaosituacao_create = Permission::where('name', '=', 'solicitacaosituacao.create')->get()->first();
		$solicitacaosituacao_edit = Permission::where('name', '=', 'solicitacaosituacao.edit')->get()->first();  
		$solicitacaosituacao_delete = Permission::where('name', '=', 'solicitacaosituacao.delete')->get()->first();
		$solicitacaosituacao_show = Permission::where('name', '=', 'solicitacaosituacao.show')->get()->first();  
		$solicitacaosituacao_export = Permission::where('name', '=', 'solicitacaosituacao.export')->get()->first();
		// para solicitações
		$solicitacao_index = Permission::where('name', '=', 'solicitacao.index')->get()->first(); 
		$solicitacao_create = Permission::where('name', '=', 'solicitacao.create')->get()->first();
		$solicitacao_edit = Permission::where('name', '=', 'solicitacao.edit')->get()->first();  
		$solicitacao_delete = Permission::where('name', '=', 'solicitacao.delete')->get()->first();
		$solicitacao_show = Permission::where('name', '=', 'solicitacao.show')->get()->first();  
		$solicitacao_export = Permission::where('name', '=', 'solicitacao.export')->get()->first();
		// Solicitações (tramitacões)
		$solicitacao_tramitacao_create = Permission::where('name', '=', 'solicitacao.tramitacao.create')->get()->first(); 
		$solicitacao_tramitacao_delete = Permission::where('name', '=', 'solicitacao.tramitacao.delete')->get()->first();
		// Solicitações (anexos)
		$solicitacao_anexo_create = Permission::where('name', '=', 'solicitacao.anexo.create')->get()->first(); 
		$solicitacao_anexo_delete = Permission::where('name', '=', 'solicitacao.anexo.delete')->get()->first();
		// para grupos de trabalho
		$grupo_index = Permission::where('name', '=', 'grupo.index')->get()->first(); 
		$grupo_create = Permission::where('name', '=', 'grupo.create')->get()->first();
		$grupo_edit = Permission::where('name', '=', 'grupo.edit')->get()->first();  
		$grupo_delete = Permission::where('name', '=', 'grupo.delete')->get()->first();
		$grupo_show = Permission::where('name', '=', 'grupo.show')->get()->first();  
		$grupo_export = Permission::where('name', '=', 'grupo.export')->get()->first();
		// para respostas
		$resposta_index = Permission::where('name', '=', 'resposta.index')->get()->first(); 
		$resposta_create = Permission::where('name', '=', 'resposta.create')->get()->first();
		$resposta_edit = Permission::where('name', '=', 'resposta.edit')->get()->first();  
		$resposta_delete = Permission::where('name', '=', 'resposta.delete')->get()->first();
		$resposta_show = Permission::where('name', '=', 'resposta.show')->get()->first();  
		$resposta_export = Permission::where('name', '=', 'resposta.export')->get()->first();



		// salva os relacionamentos entre perfil e suas permissões
		
		// o administrador tem acesso total ao sistema, incluindo
		// configurações avançadas de desenvolvimento
		$administrador_perfil->permissions()->attach($user_index);
		$administrador_perfil->permissions()->attach($user_create);
		$administrador_perfil->permissions()->attach($user_edit);
		$administrador_perfil->permissions()->attach($user_delete);
		$administrador_perfil->permissions()->attach($user_show);
		$administrador_perfil->permissions()->attach($user_export);
		$administrador_perfil->permissions()->attach($role_index);
		$administrador_perfil->permissions()->attach($role_create);
		$administrador_perfil->permissions()->attach($role_edit);
		$administrador_perfil->permissions()->attach($role_delete);
		$administrador_perfil->permissions()->attach($role_show);
		$administrador_perfil->permissions()->attach($role_export);
		$administrador_perfil->permissions()->attach($permission_index);
		$administrador_perfil->permissions()->attach($permission_create);
		$administrador_perfil->permissions()->attach($permission_edit);
		$administrador_perfil->permissions()->attach($permission_delete);
		$administrador_perfil->permissions()->attach($permission_show);
		$administrador_perfil->permissions()->attach($permission_export);
		#permissões para funcionários
		$administrador_perfil->permissions()->attach($funcionario_index);
		$administrador_perfil->permissions()->attach($funcionario_create);
		$administrador_perfil->permissions()->attach($funcionario_edit);
		$administrador_perfil->permissions()->attach($funcionario_delete);
		$administrador_perfil->permissions()->attach($funcionario_show);
		$administrador_perfil->permissions()->attach($funcionario_export);
		#permissões para setores
		$administrador_perfil->permissions()->attach($setor_index);
		$administrador_perfil->permissions()->attach($setor_create);
		$administrador_perfil->permissions()->attach($setor_edit);
		$administrador_perfil->permissions()->attach($setor_delete);
		$administrador_perfil->permissions()->attach($setor_show);
		$administrador_perfil->permissions()->attach($setor_export);
		#permissões para tipos de protocolos
		$administrador_perfil->permissions()->attach($protocolotipo_index);
		$administrador_perfil->permissions()->attach($protocolotipo_create);
		$administrador_perfil->permissions()->attach($protocolotipo_edit);
		$administrador_perfil->permissions()->attach($protocolotipo_delete);
		$administrador_perfil->permissions()->attach($protocolotipo_show);
		$administrador_perfil->permissions()->attach($protocolotipo_export);
		#permissões para tipos de protocolos
		$administrador_perfil->permissions()->attach($protocolosituacao_index);
		$administrador_perfil->permissions()->attach($protocolosituacao_create);
		$administrador_perfil->permissions()->attach($protocolosituacao_edit);
		$administrador_perfil->permissions()->attach($protocolosituacao_delete);
		$administrador_perfil->permissions()->attach($protocolosituacao_show);
		$administrador_perfil->permissions()->attach($protocolosituacao_export);
		#permissões para tipos de periodo
		$administrador_perfil->permissions()->attach($periodotipo_index);
		$administrador_perfil->permissions()->attach($periodotipo_create);
		$administrador_perfil->permissions()->attach($periodotipo_edit);
		$administrador_perfil->permissions()->attach($periodotipo_delete);
		$administrador_perfil->permissions()->attach($periodotipo_show);
		$administrador_perfil->permissions()->attach($periodotipo_export);
		#permissões protocolos
		$administrador_perfil->permissions()->attach($protocolo_index);
		$administrador_perfil->permissions()->attach($protocolo_create);
		$administrador_perfil->permissions()->attach($protocolo_edit);
		$administrador_perfil->permissions()->attach($protocolo_delete);
		$administrador_perfil->permissions()->attach($protocolo_show);
		$administrador_perfil->permissions()->attach($protocolo_export);
		$administrador_perfil->permissions()->attach($protocolo_encaminhar);
		$administrador_perfil->permissions()->attach($protocolo_concluir);
		#permissões protocolos (periodos)
		$administrador_perfil->permissions()->attach($periodo_create);
		$administrador_perfil->permissions()->attach($periodo_delete);
		#permissões protocolos (tramitações)
		$administrador_perfil->permissions()->attach($tramitacao_create);
		$administrador_perfil->permissions()->attach($tramitacao_delete);
		#permissões protocolos (anexos)
		$administrador_perfil->permissions()->attach($protocolo_anexo_create);
		$administrador_perfil->permissions()->attach($protocolo_anexo_delete);
		#permissões para tipos de memorando
		$administrador_perfil->permissions()->attach($memorandotipo_index);
		$administrador_perfil->permissions()->attach($memorandotipo_create);
		$administrador_perfil->permissions()->attach($memorandotipo_edit);
		$administrador_perfil->permissions()->attach($memorandotipo_delete);
		$administrador_perfil->permissions()->attach($memorandotipo_show);
		$administrador_perfil->permissions()->attach($memorandotipo_export);
		#permissões para situações dos memorandos
		$administrador_perfil->permissions()->attach($memorandosituacao_index);
		$administrador_perfil->permissions()->attach($memorandosituacao_create);
		$administrador_perfil->permissions()->attach($memorandosituacao_edit);
		$administrador_perfil->permissions()->attach($memorandosituacao_delete);
		$administrador_perfil->permissions()->attach($memorandosituacao_show);
		$administrador_perfil->permissions()->attach($memorandosituacao_export);
		#permissões para memorandos
		$administrador_perfil->permissions()->attach($memorando_index);
		$administrador_perfil->permissions()->attach($memorando_create);
		$administrador_perfil->permissions()->attach($memorando_edit);
		$administrador_perfil->permissions()->attach($memorando_delete);
		$administrador_perfil->permissions()->attach($memorando_show);
		$administrador_perfil->permissions()->attach($memorando_export);
		#permissões memorandos (tramitações)
		$administrador_perfil->permissions()->attach($memorando_tramitacao_create);
		$administrador_perfil->permissions()->attach($memorando_tramitacao_delete);
		#permissões memorandos (anexos)
		$administrador_perfil->permissions()->attach($memorando_anexo_create);
		$administrador_perfil->permissions()->attach($memorando_anexo_delete);
		#permissões para tipos de ofícios
		$administrador_perfil->permissions()->attach($oficiotipo_index);
		$administrador_perfil->permissions()->attach($oficiotipo_create);
		$administrador_perfil->permissions()->attach($oficiotipo_edit);
		$administrador_perfil->permissions()->attach($oficiotipo_delete);
		$administrador_perfil->permissions()->attach($oficiotipo_show);
		$administrador_perfil->permissions()->attach($oficiotipo_export);
		#permissões para situações dos ofícios
		$administrador_perfil->permissions()->attach($oficiosituacao_index);
		$administrador_perfil->permissions()->attach($oficiosituacao_create);
		$administrador_perfil->permissions()->attach($oficiosituacao_edit);
		$administrador_perfil->permissions()->attach($oficiosituacao_delete);
		$administrador_perfil->permissions()->attach($oficiosituacao_show);
		$administrador_perfil->permissions()->attach($oficiosituacao_export);
		#permissões dos ofícios
		$administrador_perfil->permissions()->attach($oficio_index);
		$administrador_perfil->permissions()->attach($oficio_create);
		$administrador_perfil->permissions()->attach($oficio_edit);
		$administrador_perfil->permissions()->attach($oficio_delete);
		$administrador_perfil->permissions()->attach($oficio_show);
		$administrador_perfil->permissions()->attach($oficio_export);
		#permissões ofícios (tramitações)
		$administrador_perfil->permissions()->attach($oficio_tramitacao_create);
		$administrador_perfil->permissions()->attach($oficio_tramitacao_delete);
		#permissões ofícios (anexos)
		$administrador_perfil->permissions()->attach($oficio_anexo_create);
		$administrador_perfil->permissions()->attach($oficio_anexo_delete);
		#permissões para tipos de solicitações
		$administrador_perfil->permissions()->attach($solicitacaotipo_index);
		$administrador_perfil->permissions()->attach($solicitacaotipo_create);
		$administrador_perfil->permissions()->attach($solicitacaotipo_edit);
		$administrador_perfil->permissions()->attach($solicitacaotipo_delete);
		$administrador_perfil->permissions()->attach($solicitacaotipo_show);
		$administrador_perfil->permissions()->attach($solicitacaotipo_export);
		#permissões para situações das solicitacaos
		$administrador_perfil->permissions()->attach($solicitacaosituacao_index);
		$administrador_perfil->permissions()->attach($solicitacaosituacao_create);
		$administrador_perfil->permissions()->attach($solicitacaosituacao_edit);
		$administrador_perfil->permissions()->attach($solicitacaosituacao_delete);
		$administrador_perfil->permissions()->attach($solicitacaosituacao_show);
		$administrador_perfil->permissions()->attach($solicitacaosituacao_export);
		#permissões das solicitações
		$administrador_perfil->permissions()->attach($solicitacao_index);
		$administrador_perfil->permissions()->attach($solicitacao_create);
		$administrador_perfil->permissions()->attach($solicitacao_edit);
		$administrador_perfil->permissions()->attach($solicitacao_delete);
		$administrador_perfil->permissions()->attach($solicitacao_show);
		$administrador_perfil->permissions()->attach($solicitacao_export);
		#permissões solicitações (tramitações)
		$administrador_perfil->permissions()->attach($solicitacao_tramitacao_create);
		$administrador_perfil->permissions()->attach($solicitacao_tramitacao_delete);
		#permissões solicitações (anexos)
		$administrador_perfil->permissions()->attach($solicitacao_anexo_create);
		$administrador_perfil->permissions()->attach($solicitacao_anexo_delete);
		# Grupos de trabalho
		$administrador_perfil->permissions()->attach($grupo_index);
		$administrador_perfil->permissions()->attach($grupo_create);
		$administrador_perfil->permissions()->attach($grupo_edit);
		$administrador_perfil->permissions()->attach($grupo_delete);
		$administrador_perfil->permissions()->attach($grupo_show);
		$administrador_perfil->permissions()->attach($grupo_export);
		# Respostas
		$administrador_perfil->permissions()->attach($resposta_index);
		$administrador_perfil->permissions()->attach($resposta_create);
		$administrador_perfil->permissions()->attach($resposta_edit);
		$administrador_perfil->permissions()->attach($resposta_delete);
		$administrador_perfil->permissions()->attach($resposta_show);
		$administrador_perfil->permissions()->attach($resposta_export);




		//
		// o gerente (diretor) pode gerenciar os operadores do sistema
		$gerente_perfil->permissions()->attach($user_index);
		$gerente_perfil->permissions()->attach($user_create);
		$gerente_perfil->permissions()->attach($user_edit);
		$gerente_perfil->permissions()->attach($user_show);
		$gerente_perfil->permissions()->attach($user_export);
		#permissões para funcionários
		$gerente_perfil->permissions()->attach($funcionario_index);
		$gerente_perfil->permissions()->attach($funcionario_create);
		$gerente_perfil->permissions()->attach($funcionario_edit);
		$gerente_perfil->permissions()->attach($funcionario_show);
		$gerente_perfil->permissions()->attach($funcionario_export);
		#permissões para setores
		$gerente_perfil->permissions()->attach($setor_index);
		$gerente_perfil->permissions()->attach($setor_create);
		$gerente_perfil->permissions()->attach($setor_edit);
		$gerente_perfil->permissions()->attach($setor_show);
		$gerente_perfil->permissions()->attach($setor_export);
		#permissões para tipos de protocolo
		$gerente_perfil->permissions()->attach($protocolotipo_index);
		$gerente_perfil->permissions()->attach($protocolotipo_create);
		$gerente_perfil->permissions()->attach($protocolotipo_edit);
		$gerente_perfil->permissions()->attach($protocolotipo_show);
		$gerente_perfil->permissions()->attach($protocolotipo_export);
		#permissões para situações de protocolo
		$gerente_perfil->permissions()->attach($protocolosituacao_index);
		$gerente_perfil->permissions()->attach($protocolosituacao_create);
		$gerente_perfil->permissions()->attach($protocolosituacao_edit);
		$gerente_perfil->permissions()->attach($protocolosituacao_show);
		$gerente_perfil->permissions()->attach($protocolosituacao_export);
		#permissões para tipos de periodo
		$gerente_perfil->permissions()->attach($periodotipo_index);
		$gerente_perfil->permissions()->attach($periodotipo_create);
		$gerente_perfil->permissions()->attach($periodotipo_edit);
		$gerente_perfil->permissions()->attach($periodotipo_show);
		$gerente_perfil->permissions()->attach($periodotipo_export);
		#permissões para protocolos
		$gerente_perfil->permissions()->attach($protocolo_index);
		$gerente_perfil->permissions()->attach($protocolo_create);
		$gerente_perfil->permissions()->attach($protocolo_edit);
		$gerente_perfil->permissions()->attach($protocolo_show);
		$gerente_perfil->permissions()->attach($protocolo_export);
		$gerente_perfil->permissions()->attach($protocolo_delete); // gerente pode excluir os protocolos
		$gerente_perfil->permissions()->attach($protocolo_encaminhar);
		$gerente_perfil->permissions()->attach($protocolo_concluir);
		#permissões para protocolos (periodos)
		$gerente_perfil->permissions()->attach($periodo_create);
		$gerente_perfil->permissions()->attach($periodo_delete);
		#permissões para protocolos (tramitações)
		$gerente_perfil->permissions()->attach($tramitacao_create);
		$gerente_perfil->permissions()->attach($tramitacao_delete);
		#permissões para protocolos (anexos)
		$gerente_perfil->permissions()->attach($protocolo_anexo_create);
		$gerente_perfil->permissions()->attach($protocolo_anexo_delete);
		#permissões para tipos de memorando
		$gerente_perfil->permissions()->attach($memorandotipo_index);
		$gerente_perfil->permissions()->attach($memorandotipo_create);
		$gerente_perfil->permissions()->attach($memorandotipo_edit);
		$gerente_perfil->permissions()->attach($memorandotipo_show);
		$gerente_perfil->permissions()->attach($memorandotipo_export);
		#permissões para situações dos memorando
		$gerente_perfil->permissions()->attach($memorandosituacao_index);
		$gerente_perfil->permissions()->attach($memorandosituacao_create);
		$gerente_perfil->permissions()->attach($memorandosituacao_edit);
		$gerente_perfil->permissions()->attach($memorandosituacao_show);
		$gerente_perfil->permissions()->attach($memorandosituacao_export);
		#permissões para situações dos memorando
		$gerente_perfil->permissions()->attach($memorando_index);
		$gerente_perfil->permissions()->attach($memorando_create);
		$gerente_perfil->permissions()->attach($memorando_edit);
		$gerente_perfil->permissions()->attach($memorando_show);
		$gerente_perfil->permissions()->attach($memorando_export);
		$gerente_perfil->permissions()->attach($memorando_delete);
		#permissões memorandos (tramitações)
		$gerente_perfil->permissions()->attach($memorando_tramitacao_create);
		$gerente_perfil->permissions()->attach($memorando_tramitacao_delete);
		#permissões memorandos (anexos)
		$gerente_perfil->permissions()->attach($memorando_anexo_create);
		$gerente_perfil->permissions()->attach($memorando_anexo_delete);
		#permissões para tipos de ofício
		$gerente_perfil->permissions()->attach($oficiotipo_index);
		$gerente_perfil->permissions()->attach($oficiotipo_create);
		$gerente_perfil->permissions()->attach($oficiotipo_edit);
		$gerente_perfil->permissions()->attach($oficiotipo_show);
		$gerente_perfil->permissions()->attach($oficiotipo_export);
		#permissões para situações dos ofício
		$gerente_perfil->permissions()->attach($oficiosituacao_index);
		$gerente_perfil->permissions()->attach($oficiosituacao_create);
		$gerente_perfil->permissions()->attach($oficiosituacao_edit);
		$gerente_perfil->permissions()->attach($oficiosituacao_show);
		$gerente_perfil->permissions()->attach($oficiosituacao_export);
		#permissões para os ofícios
		$gerente_perfil->permissions()->attach($oficio_index);
		$gerente_perfil->permissions()->attach($oficio_create);
		$gerente_perfil->permissions()->attach($oficio_edit);
		$gerente_perfil->permissions()->attach($oficio_show);
		$gerente_perfil->permissions()->attach($oficio_export);
		$gerente_perfil->permissions()->attach($oficio_delete);
		#permissões ofícios (tramitações)
		$gerente_perfil->permissions()->attach($oficio_tramitacao_create);
		$gerente_perfil->permissions()->attach($oficio_tramitacao_delete);
		#permissões ofícios (anexos)
		$gerente_perfil->permissions()->attach($oficio_anexo_create);
		$gerente_perfil->permissions()->attach($oficio_anexo_delete);
		#permissões para tipos de solicitações
		$gerente_perfil->permissions()->attach($solicitacaotipo_index);
		$gerente_perfil->permissions()->attach($solicitacaotipo_create);
		$gerente_perfil->permissions()->attach($solicitacaotipo_edit);
		$gerente_perfil->permissions()->attach($solicitacaotipo_show);
		$gerente_perfil->permissions()->attach($solicitacaotipo_export);
		#permissões para situações dos ofício
		$gerente_perfil->permissions()->attach($solicitacaosituacao_index);
		$gerente_perfil->permissions()->attach($solicitacaosituacao_create);
		$gerente_perfil->permissions()->attach($solicitacaosituacao_edit);
		$gerente_perfil->permissions()->attach($solicitacaosituacao_show);
		$gerente_perfil->permissions()->attach($solicitacaosituacao_export);
		#permissões para as solicitações
		$gerente_perfil->permissions()->attach($solicitacao_index);
		$gerente_perfil->permissions()->attach($solicitacao_create);
		$gerente_perfil->permissions()->attach($solicitacao_edit);
		$gerente_perfil->permissions()->attach($solicitacao_show);
		$gerente_perfil->permissions()->attach($solicitacao_export);
		$gerente_perfil->permissions()->attach($solicitacao_delete);
		#permissões solicitações (tramitações)
		$gerente_perfil->permissions()->attach($solicitacao_tramitacao_create);
		$gerente_perfil->permissions()->attach($solicitacao_tramitacao_delete);
		#permissões solicitações (anexos)
		$gerente_perfil->permissions()->attach($solicitacao_anexo_create);
		$gerente_perfil->permissions()->attach($solicitacao_anexo_delete);
		# grupos de trabalho
		$gerente_perfil->permissions()->attach($grupo_index);
		$gerente_perfil->permissions()->attach($grupo_create);
		$gerente_perfil->permissions()->attach($grupo_edit);
		$gerente_perfil->permissions()->attach($grupo_show);
		$gerente_perfil->permissions()->attach($grupo_export);
		# respostas
		$gerente_perfil->permissions()->attach($resposta_index);
		$gerente_perfil->permissions()->attach($resposta_create);
		$gerente_perfil->permissions()->attach($resposta_edit);
		$gerente_perfil->permissions()->attach($resposta_show);
		$gerente_perfil->permissions()->attach($resposta_export);





		// o operador é o nível de operação do sistema não pode criar
		// outros operadores
		$operador_perfil->permissions()->attach($user_index);
		$operador_perfil->permissions()->attach($user_show);
		$operador_perfil->permissions()->attach($user_export);
		#permissões para funcionários, o operador pode editar e criar
		$operador_perfil->permissions()->attach($funcionario_index);
		$operador_perfil->permissions()->attach($funcionario_create);
		$operador_perfil->permissions()->attach($funcionario_edit);		
		$operador_perfil->permissions()->attach($funcionario_show);
		$operador_perfil->permissions()->attach($funcionario_export);
		#permissões para setores, o operador pode editar e criar
		$operador_perfil->permissions()->attach($setor_index);
		$operador_perfil->permissions()->attach($setor_create);
		$operador_perfil->permissions()->attach($setor_edit);
		$operador_perfil->permissions()->attach($setor_show);
		$operador_perfil->permissions()->attach($setor_export);				
		#permissões para tipos de protocolo
		$operador_perfil->permissions()->attach($protocolotipo_index);
		$operador_perfil->permissions()->attach($protocolotipo_show);
		$operador_perfil->permissions()->attach($protocolotipo_export);
		#permissões para situações do protocolo
		$operador_perfil->permissions()->attach($protocolosituacao_index);
		$operador_perfil->permissions()->attach($protocolosituacao_show);
		$operador_perfil->permissions()->attach($protocolosituacao_export);
		#permissões para tipos de periodo
		$operador_perfil->permissions()->attach($periodotipo_index);
		$operador_perfil->permissions()->attach($periodotipo_show);
		$operador_perfil->permissions()->attach($periodotipo_export);
		#permissões para protocolos, o operador pode editar e criar
		$operador_perfil->permissions()->attach($protocolo_index);
		$operador_perfil->permissions()->attach($protocolo_create);
		$operador_perfil->permissions()->attach($protocolo_edit);		
		$operador_perfil->permissions()->attach($protocolo_show);
		$operador_perfil->permissions()->attach($protocolo_export);
		$operador_perfil->permissions()->attach($protocolo_encaminhar);
		$operador_perfil->permissions()->attach($protocolo_concluir);
		#permissões para protocolos (periodos)
		$operador_perfil->permissions()->attach($periodo_create);
		$operador_perfil->permissions()->attach($periodo_delete); // na dúvida aqui
		#permissões para protocolos (tramitações)
		$operador_perfil->permissions()->attach($tramitacao_create);
		#permissões para protocolos (anexos)
		$operador_perfil->permissions()->attach($protocolo_anexo_create);
		// melhor não $operador_perfil->permissions()->attach($tramitacao_delete);
		#permissões para tipos de memorando
		$operador_perfil->permissions()->attach($memorandotipo_index);
		$operador_perfil->permissions()->attach($memorandotipo_show);
		$operador_perfil->permissions()->attach($memorandotipo_export);
		#permissões para situações dos memorandos
		$operador_perfil->permissions()->attach($memorandosituacao_index);
		$operador_perfil->permissions()->attach($memorandosituacao_show);
		$operador_perfil->permissions()->attach($memorandosituacao_export);
		#permissões para memorandos, o operador pode editar e criar
		$operador_perfil->permissions()->attach($memorando_index);
		$operador_perfil->permissions()->attach($memorando_create);
		$operador_perfil->permissions()->attach($memorando_edit);		
		$operador_perfil->permissions()->attach($memorando_show);
		$operador_perfil->permissions()->attach($memorando_export);
		#permissões memorandos (tramitações)
		$operador_perfil->permissions()->attach($memorando_tramitacao_create);
		#permissões memorandos (anexos)
		$operador_perfil->permissions()->attach($memorando_anexo_create);
		#permissões para tipos de oficio
		$operador_perfil->permissions()->attach($oficiotipo_index);
		$operador_perfil->permissions()->attach($oficiotipo_show);
		$operador_perfil->permissions()->attach($oficiotipo_export);
		#permissões para situações dos oficios
		$operador_perfil->permissions()->attach($oficiosituacao_index);
		$operador_perfil->permissions()->attach($oficiosituacao_show);
		$operador_perfil->permissions()->attach($oficiosituacao_export);
		#permissões para ofícios, o operador pode editar e criar
		$operador_perfil->permissions()->attach($oficio_index);
		$operador_perfil->permissions()->attach($oficio_create);
		$operador_perfil->permissions()->attach($oficio_edit);		
		$operador_perfil->permissions()->attach($oficio_show);
		$operador_perfil->permissions()->attach($oficio_export);
		#permissões ofícios (tramitações)
		$operador_perfil->permissions()->attach($oficio_anexo_create);
		#permissões para tipos de solicitações
		$operador_perfil->permissions()->attach($solicitacaotipo_index);
		$operador_perfil->permissions()->attach($solicitacaotipo_show);
		$operador_perfil->permissions()->attach($solicitacaotipo_export);
		#permissões para situações das solicitações
		$operador_perfil->permissions()->attach($solicitacaosituacao_index);
		$operador_perfil->permissions()->attach($solicitacaosituacao_show);
		$operador_perfil->permissions()->attach($solicitacaosituacao_export);
		#permissões para solicitações, o operador pode editar e criar
		$operador_perfil->permissions()->attach($solicitacao_index);
		$operador_perfil->permissions()->attach($solicitacao_create);
		$operador_perfil->permissions()->attach($solicitacao_edit);		
		$operador_perfil->permissions()->attach($solicitacao_show);
		$operador_perfil->permissions()->attach($solicitacao_export);
		#permissões para solicitações, o operador pode editar e criar
		$operador_perfil->permissions()->attach($solicitacao_index);
		$operador_perfil->permissions()->attach($solicitacao_create);
		$operador_perfil->permissions()->attach($solicitacao_edit);		
		$operador_perfil->permissions()->attach($solicitacao_show);
		$operador_perfil->permissions()->attach($solicitacao_export);
		#permissões para solicitações (tramitações)
		$operador_perfil->permissions()->attach($solicitacao_tramitacao_create);
		#permissões para solicitações (anexos)
		$operador_perfil->permissions()->attach($solicitacao_anexo_create);
		# grupos de trabalho
		$operador_perfil->permissions()->attach($grupo_index);
		$operador_perfil->permissions()->attach($grupo_show);
		$operador_perfil->permissions()->attach($grupo_export);
		# respostas
		$operador_perfil->permissions()->attach($resposta_index);
		$operador_perfil->permissions()->attach($resposta_show);
		$operador_perfil->permissions()->attach($resposta_export);




		// leitura é um tipo de operador que só pode ler
		// os dados na tela
		$leitor_perfil->permissions()->attach($user_index);
		$leitor_perfil->permissions()->attach($user_show);
		#permissões para funcionários
		$leitor_perfil->permissions()->attach($funcionario_index);
		$leitor_perfil->permissions()->attach($funcionario_show);
		#permissões para setores
		$leitor_perfil->permissions()->attach($setor_index);
		$leitor_perfil->permissions()->attach($setor_show);
		#permissões para tipos de protocolo
		$leitor_perfil->permissions()->attach($protocolotipo_index);
		$leitor_perfil->permissions()->attach($protocolotipo_show);
		#permissões para situacao do protocolo
		$leitor_perfil->permissions()->attach($protocolosituacao_index);
		$leitor_perfil->permissions()->attach($protocolosituacao_show);
		#permissões para tipos de periodo
		$leitor_perfil->permissions()->attach($periodotipo_index);
		$leitor_perfil->permissions()->attach($periodotipo_show);
		#permissões para protocolo
		$leitor_perfil->permissions()->attach($protocolo_index);
		$leitor_perfil->permissions()->attach($protocolo_show);
		#permissões para protocolos (periodos)
		// leitor não pode criar ou alterar periodos, mas pode consultar
		#permissões para protocolos (tramitações)
		// leitor não pode criar nem alterar, mas pode consultas
		#permissões para tipos de memorando
		$leitor_perfil->permissions()->attach($memorandotipo_index);
		$leitor_perfil->permissions()->attach($memorandotipo_show);
		#permissões para situações dos memorandos
		$leitor_perfil->permissions()->attach($memorandosituacao_index);
		$leitor_perfil->permissions()->attach($memorandosituacao_show);
		#permissões para memorandos
		$leitor_perfil->permissions()->attach($memorando_index);
		$leitor_perfil->permissions()->attach($memorando_show);
		#permissões para tipos de ofício
		$leitor_perfil->permissions()->attach($oficiotipo_index);
		$leitor_perfil->permissions()->attach($oficiotipo_show);
		#permissões para situações dos ofício
		$leitor_perfil->permissions()->attach($oficiosituacao_index);
		$leitor_perfil->permissions()->attach($oficiosituacao_show);
		#permissões para os ofício
		$leitor_perfil->permissions()->attach($oficio_index);
		$leitor_perfil->permissions()->attach($oficio_show);
		#permissões para tipos de solicitações
		$leitor_perfil->permissions()->attach($solicitacaotipo_index);
		$leitor_perfil->permissions()->attach($solicitacaotipo_show);
		#permissões para situações dos ofício
		$leitor_perfil->permissions()->attach($solicitacaosituacao_index);
		$leitor_perfil->permissions()->attach($solicitacaosituacao_show);
		#permissões para as solicitações
		$leitor_perfil->permissions()->attach($solicitacao_index);
		$leitor_perfil->permissions()->attach($solicitacao_show);
		# grupos de trabalho
		$leitor_perfil->permissions()->attach($grupo_index);
		$leitor_perfil->permissions()->attach($grupo_show);
		# resposta
		$leitor_perfil->permissions()->attach($grupo_index);
		$leitor_perfil->permissions()->attach($grupo_show);


		echo "usuário Administrador: adm@mail.br senha:123456  \n";		
		echo "usuário Gerente: gerente@mail.br senha:123456  \n";		
		echo "usuário Operacional: operador@mail.br senha:123456  \n";		
		echo "usuário Somente Leitura: leitura@mail.br senha:123456  \n";		

    }
}
