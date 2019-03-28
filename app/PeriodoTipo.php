<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodoTipo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function periodos()
    {
        return $this->belongsToMany('App\Periodo');
    }
}
