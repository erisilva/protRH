<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OficioTipo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function oficios()
    {
        return $this->hasMany('App\Oficio');
    }

    public function oficioTramitacaos()
    {
        return $this->hasMany('App\OficioTramitacao');
    }
}
