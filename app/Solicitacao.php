<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitacao extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'chave', 'identificacao', 'solicitacao_tipo_id', 'solicitacao_situacao_id', 'user_id', 'grupo_id', 'concluido_mensagem', 'concluido', 'concluido_em', 'resposta_id', 'encaminhado_em'
    ];

    protected $dates = ['created_at', 'concluido_em', 'encaminhado_em'];

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

    /**
     * Pega todos anexos de um protocolo
     */
    public function anexos()
    {
        return $this->morphMany('App\Anexo', 'anexoable');
    }

    public function grupo()
    {
        return $this->belongsTo('App\Grupo');
    }

    public function resposta()
    {
        return $this->belongsTo('App\Resposta');
    }
}
