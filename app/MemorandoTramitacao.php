<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemorandoTramitacao extends Model
{
    protected $fillable = [
        'descricao', 'funcionario_id', 'setor_id', 'user_id', 'memorando_id'
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

    public function memorando()
    {
        return $this->belongsTo('App\Memorando');
    }
}
