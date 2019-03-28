<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = [
        'inicio', 'fim', 'periodo_tipo_id', 'protocolo_id'
    ];

    protected $dates = ['inicio', 'fim'];

    public function protocolo()
    {
        return $this->belongsTo('App\Protocolo');
    }

    public function periodoTipo()
    {
        return $this->belongsTo('App\PeriodoTipo');
    }
}
