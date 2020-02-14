<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oficio extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'chave', 'oficio_tipo_id', 'oficio_situacao_id', 'user_id', 'grupo_id', 'concluido_mensagem', 'concluido', 'concluido_em', 'resposta_id', 'encaminhado_em'
    ];

    protected $dates = ['created_at', 'concluido_em', 'encaminhado_em'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function oficioTipo()
    {
        return $this->belongsTo('App\OficioTipo');
    }

    public function oficioSituacao()
    {
        return $this->belongsTo('App\OficioSituacao');
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
