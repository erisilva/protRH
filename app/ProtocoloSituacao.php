<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProtocoloSituacao extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function protocolos()
    {
        return $this->hasMany('Protocolo');
    }    
}
