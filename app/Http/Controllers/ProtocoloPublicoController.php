<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Protocolo;
use App\Tramitacao;
use App\Periodo;

class ProtocoloPublicoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function buscar($chave)
    {
        $protocolo = Protocolo::where('chave', '=', $chave)->get()->first();

        if (!isset($protocolo)){
        	abort(404);
        }

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $protocolo->id)->orderBy('id', 'desc')->get();

        $periodos = Periodo::where('protocolo_id', '=', $protocolo->id)->orderBy('id', 'asc')->get();

        return view('protocolos.publico', compact('protocolo', 'tramitacoes', 'periodos'));
    }
}
