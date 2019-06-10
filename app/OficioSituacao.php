<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OficioSituacao extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function oficios()
    {
        return $this->hasMany('App\Oficio');
    }
}
