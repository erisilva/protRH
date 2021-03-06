<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memorando extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'chave', 'memorando_tipo_id', 'memorando_situacao_id', 'user_id', 'grupo_id', 'concluido_mensagem', 'concluido', 'concluido_em', 'resposta_id', 'encaminhado_em'
    ];

    protected $dates = ['created_at', 'concluido_em', 'encaminhado_em'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function memorandoTipo()
    {
        return $this->belongsTo('App\MemorandoTipo');
    }

    public function memorandoSituacao()
    {
        return $this->belongsTo('App\MemorandoSituacao');
    }

    public function memorandoTramitacaos()
    {
        return $this->hasMany('App\MemorandoTramitacao');
    }

    /**
     * Pega todos anexos de um protocolo
     */
    public function anexos()
    {
        return $this->morphMany('App\Anexo', 'anexoable');
    } 

    public function resposta()
    {
        return $this->belongsTo('App\Resposta');
    }

    public function grupo()
    {
        return $this->belongsTo('App\Grupo');
    }   
}
