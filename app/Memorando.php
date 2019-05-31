<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memorando extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'numero', 'chave', 'memorando_tipo_id', 'memorando_situacao_id', 'user_id'
    ];

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
}
