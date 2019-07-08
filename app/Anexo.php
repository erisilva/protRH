<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    protected $fillable = [
        'arquivoNome', 'arquivoLocal', 'arquivoUrl', 'anexoable_id', 'anexoable_type', 'user_id',
    ];

	/**
     * Get all of the owning anexoable models.
     */
    public function anexoable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }       
}
