<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoTramitacao extends Model
{
    protected $fillable = [
        'descricao', 'funcionario_id', 'setor_id', 'user_id', 'solicitacao_id'
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

    public function solicitacao()
    {
        return $this->belongsTo('App\Solicitacao');
    }
}
