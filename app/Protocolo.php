<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Protocolo extends Model
{
    protected $fillable = [
        'descricao', 'setor_id', 'funcionario_id', 'protocolo_tipo_id', 'protocolo_situacao_id', 'user_id', 'chave'
    ];


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function funcionario()
    {
        return $this->belongsTo('App\Funcionario');
    }

    public function setor()
    {
        return $this->belongsTo('App\Setor');
    }

    // Nota Mental : No caso de Modelos com nomes compostos
    // user o formato camelCase para os relacionamentos
    public function protocoloTipo()
    {
        return $this->belongsTo('App\ProtocoloTipo');
    }
    // Nota Mental : No caso de Modelos com nomes compostos
    // user o formato camelCase para os relacionamentos
    public function protocoloSituacao()
    {
        return $this->belongsTo('App\ProtocoloSituacao');
    }

    public function periodos()
    {
        return $this->belongsToMany('App\Periodo');
    }

    public function tramitacaos()
    {
        return $this->belongsToMany('App\Tramitacao');
    }
}
