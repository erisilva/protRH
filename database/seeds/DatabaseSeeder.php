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



        $this->call(AclSeeder::class);
        
    }
}
