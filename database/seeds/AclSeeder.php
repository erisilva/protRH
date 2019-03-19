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

		echo "usuário Administrador: adm@mail.br senha:123456  \n";		
		echo "usuário Gerente: gerente@mail.br senha:123456  \n";		
		echo "usuário Operacional: operador@mail.br senha:123456  \n";		
		echo "usuário Somente Leitura: leitura@mail.br senha:123456  \n";		

    }
}
