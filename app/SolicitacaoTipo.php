<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoTipo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function solicitacaos()
    {
        return $this->hasMany('App\Solicitacao');
    }

}
