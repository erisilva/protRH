<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tramitacao extends Model
{
    protected $fillable = [
        'descricao', 'funcionario_id', 'setor_id', 'user_id', 'protocolo_id'
    ];

    public function funcionario()
    {
        return $this->belongsTo('App\Funcionario');
    }

	public function setor()
    {
        return $this->belongsTo('App\Setor');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function protocolo()
    {
        return $this->belongsTo('App\Protocolo');
    }
}
