<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProtocoloTipo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function protocolo()
    {
        return $this->hasMany('Protocolo');
    }    
}
