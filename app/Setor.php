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
        return $this->hasMany('Protocolo');
    }     
}
