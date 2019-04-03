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
		// para protocolos (periodos)
		$periodo_create = Permission::where('name', '=', 'periodo.create')->get()->first(); 
		$periodo_delete = Permission::where('name', '=', 'periodo.delete')->get()->first();
		// para protocolos (tramitacões)
		$tramitacao_create = Permission::where('name', '=', 'tramitacao.create')->get()->first(); 
		$tramitacao_delete = Permission::where('name', '=', 'tramitacao.delete')->get()->first();


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
		#permissões protocolos (periodos)
		$administrador_perfil->permissions()->attach($periodo_create);
		$administrador_perfil->permissions()->attach($periodo_delete);
		#permissões protocolos (tramitações)
		$administrador_perfil->permissions()->attach($tramitacao_create);
		$administrador_perfil->permissions()->attach($tramitacao_delete);


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
		#permissões para protocolos (periodos)
		$gerente_perfil->permissions()->attach($periodo_create);
		$gerente_perfil->permissions()->attach($periodo_delete);
		#permissões para protocolos (tramitações)
		$gerente_perfil->permissions()->attach($tramitacao_create);
		$gerente_perfil->permissions()->attach($tramitacao_delete);


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
		#permissões para protocolos (periodos)
		$operador_perfil->permissions()->attach($periodo_create);
		$operador_perfil->permissions()->attach($periodo_delete); // na dúvida aqui
		#permissões para protocolos (tramitações)
		$operador_perfil->permissions()->attach($tramitacao_create);
		// melhor não $operador_perfil->permissions()->attach($tramitacao_delete);


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


		echo "usuário Administrador: adm@mail.br senha:123456  \n";		
		echo "usuário Gerente: gerente@mail.br senha:123456  \n";		
		echo "usuário Operacional: operador@mail.br senha:123456  \n";		
		echo "usuário Somente Leitura: leitura@mail.br senha:123456  \n";		

    }
}
