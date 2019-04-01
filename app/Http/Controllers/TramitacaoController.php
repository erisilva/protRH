<?php

namespace App\Http\Controllers;

use App\Tramitacao;
use App\Periodo;
use App\protocolo;
use App\ProtocoloSituacao;
use App\ProtocoloTipo;
use App\PeriodoTipo;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon; // tratamento de datas
use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

use Auth; // receber o id do operador logado no sistema

class TramitacaoController extends Controller
{
    /**
     * Construtor.
     *
     * precisa estar logado ao sistema
     * precisa ter a conta ativa (access)
     *
     * @return 
     */
    public function __construct()
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input_tramitacao = $request->all();

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $input_tramitacao['user_id'] = $user->id;

        // ajusta os parametros (nomes) da entrada
        $input_tramitacao['setor_id'] = $input_tramitacao['setor_tramitacao_id'];
        $input_tramitacao['funcionario_id'] = $input_tramitacao['funcionario_tramitacao_id'];

        Tramitacao::create($input_tramitacao); //salva

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $input_tramitacao['protocolo_id'])->orderBy('id', 'desc')->get();
        
        $periodos = Periodo::where('protocolo_id', '=', $input_tramitacao['protocolo_id'])->orderBy('id', 'asc')->get();

        $protocolo = Protocolo::find($input_tramitacao['protocolo_id']);

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();

        Session::flash('create_tramitacao', 'Tramitação inserida com sucesso!');        

        return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos', 'tramitacoes');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tramitacao = Tramitacao::findOrFail($id);

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $tramitacao->protocolo_id)->orderBy('id', 'desc')->get();

        $periodos = Periodo::where('protocolo_id', '=', $tramitacao->protocolo_id)->orderBy('id', 'asc')->get();

        $protocolo = Protocolo::find($tramitacao->protocolo_id);

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();

        $tramitacao->delete();        

        Session::flash('delete_tramitacao', 'Tramitação excluída com sucesso!');

        return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos', 'tramitacoes');
    }
}
