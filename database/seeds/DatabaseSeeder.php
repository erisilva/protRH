<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PerpagesTableSeeder::class);
        $this->call(FuncionariosTableSeeder::class);
        $this->call(SetoresTableSeeder::class);
        $this->call(ProtocoloTiposTableSeeder::class);
        $this->call(ProtocoloSituacoesTableSeeder::class);
        $this->call(PeriodoTipoTableSeeder::class);
        $this->call(MemorandoTiposTableSeeder::class);
        $this->call(MemorandoSituacoesTableSeeder::class);
        $this->call(OficioTiposTableSeeder::class);
        $this->call(OficioSituacoesTableSeeder::class);
        $this->call(SolicitacaoTiposTableSeeder::class);
        $this->call(SolicitacaoSituacoesTableSeeder::class);
        $this->call(GruposTableSeeder::class);
        $this->call(RespostasTableSeeder::class);

        $this->call(AclSeeder::class);
        
    }
}
