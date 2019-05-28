<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemorandoTipo extends Model
{
    protected $fillable = [
        'descricao',
    ];

    public function memorandos()
    {
        return $this->hasMany('App\Memorando');
    }
}
