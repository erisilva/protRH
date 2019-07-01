<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoSituacao extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function solicitacaos()
    {
        return $this->hasMany('App\Solicitacao');
    }
}
