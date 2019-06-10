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
        return $this->hasMany('App\Protocolo');
    }

    public function tramitacaos()
    {
        return $this->hasMany('App\Tramitacao');
    }

    public function memorandoTramitacaos()
    {
        return $this->hasMany('App\MemorandoTramitacao');
    }

    public function oficioTramitacaos()
    {
        return $this->hasMany('App\OficioTramitacao');
    }
}
