<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oficio extends Model
{
    protected $fillable = [
        'remetente', 'observacao', 'chave', 'oficio_tipo_id', 'oficio_situacao_id', 'user_id'
    ];

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
}
