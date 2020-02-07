<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function protocolos()
    {
        return $this->hasMany('App\Protocolo');
    } 
}
