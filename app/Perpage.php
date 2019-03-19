<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perpage extends Model
{
	/*
		PerPage é um modelo que guardará a quantidade
		de registros exibos por página de consulta
		facilitando a consulta aos dados
	*/
    protected $fillable = [
        'valor', 'nome',
    ];
}
