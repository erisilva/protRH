<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Memorando;
use App\MemorandoTramitacao;


class MemorandoPublicoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $chave
     * @return \Illuminate\Http\Response
     */
    public function buscar($chave)
    {
        $memorando = Memorando::where('chave', '=', $chave)->get()->first();

        if (!isset($memorando)){
        	abort(404);
        }

        $tramitacoes = MemorandoTramitacao::where('memorando_id', '=', $memorando->id)->orderBy('id', 'desc')->get();

        return view('memorandos.publico', compact('memorando', 'tramitacoes'));
    }
}
