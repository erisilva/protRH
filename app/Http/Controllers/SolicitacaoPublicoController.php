<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Solicitacao;
use App\SolicitacaoTramitacao;


class SolicitacaoPublicoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $chave
     * @return \Illuminate\Http\Response
     */
    public function buscar($chave)
    {
        $solicitacao = Solicitacao::where('chave', '=', $chave)->get()->first();

        if (!isset($solicitacao)){
        	abort(404);
        }

        $tramitacoes = SolicitacaoTramitacao::where('solicitacao_id', '=', $solicitacao->id)->orderBy('id', 'desc')->get();

        return view('solicitacoes.publico', compact('solicitacao', 'tramitacoes'));
    }
}
