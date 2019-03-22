<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    protected $fillable = [
        'nome', 'matricula', 'email', 'numeropasta',
    ];

    public function protocolos()
    {
        return $this->hasMany('Protocolo');
    }
}
