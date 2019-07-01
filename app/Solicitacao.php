<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'chave', 'identificacao', 'solicitacao_tipo_id', 'solicitacao_situacao_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function solicitacaoTipo()
    {
        return $this->belongsTo('App\SolicitacaoTipo');
    }

    public function solicitacaoSituacao()
    {
        return $this->belongsTo('App\SolicitacaoSituacao');
    }

    public function solicitacaoTramitacaos()
    {
        return $this->hasMany('App\SolicitacaoTramitacao');
    }
}
