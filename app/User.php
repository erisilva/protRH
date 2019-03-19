<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

// OBSERVAÇÃO
// utilizo o termo operador em vez de usuário por esse
// significar usuário do SUS, ou usuário do plano, em vez de pessoa ou cliente
// dentro do sistema as telas se referenciam a operador e não usuário
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Verifica se o operador está ativo.
     *
     * @var none
     */
    public function hasAccess(){
        return ($this->active == 'Y') ? true : false;
    }

    /**
     * Perifs do operador
     *
     * @var Role
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }

    /**
     * Verifica se um operador tem determinado(s) perfil(is)
     *
     * @var Bool
     */
    public function hasRoles($roles)
    {
        $userRoles = $this->roles;
        return $roles->intersect($userRoles)->count();
    }
    
    /**
     * Verifica se um operador tem determinado perfil
     *
     * @var Bool
     */
    public function hasRole($role)
    {
        if(is_string($role)){
          $role = Role::where('name','=',$role)->firstOrFail();
        }
        return (boolean) $this->roles()->find($role->id);

    }



}
