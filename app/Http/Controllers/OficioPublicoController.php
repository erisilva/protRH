<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Oficio;
use App\OficioTramitacao;


class OficioPublicoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $chave
     * @return \Illuminate\Http\Response
     */
    public function buscar($chave)
    {
        $oficio = Oficio::where('chave', '=', $chave)->get()->first();

        if (!isset($oficio)){
        	abort(404);
        }

        $tramitacoes = oficioTramitacao::where('oficio_id', '=', $oficio->id)->orderBy('id', 'desc')->get();

        return view('oficios.publico', compact('oficio', 'tramitacoes'));
    }
}
