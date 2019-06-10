<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $fillable = [
        'codigo', 'descricao',
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
