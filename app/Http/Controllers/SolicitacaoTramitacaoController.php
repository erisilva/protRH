<?php

namespace App\Http\Controllers;

use App\SolicitacaoTramitacao;
use App\Solicitacao;
use App\SolicitacaoSituacao;
use App\SolicitacaoTipo;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon; // tratamento de datas
use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

use Auth; // receber o id do operador logado no sistema

class SolicitacaoTramitacaoController extends Controller
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
        if (Gate::denies('solicitacao.tramitacao.create')) {
            abort(403, 'Acesso negado.');
        }

        $input_tramitacao = $request->all();

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $input_tramitacao['user_id'] = $user->id;

        // ajusta os parametros (nomes) da entrada
        $input_tramitacao['setor_id'] = $input_tramitacao['setor_tramitacao_id'];
        $input_tramitacao['funcionario_id'] = $input_tramitacao['funcionario_tramitacao_id'];

        SolicitacaoTramitacao::create($input_tramitacao); //salva

        Session::flash('create_solicitacaotramitacao', 'Tramitação inserida com sucesso!');

        return Redirect::route('solicitacoes.edit', $input_tramitacao['solicitacao_id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('solicitacao.tramitacao.delete')) {
            abort(403, 'Acesso negado.');
        }
        
        $tramitacao = SolicitacaoTramitacao::findOrFail($id);

        $num_solicitacao = $tramitacao->solicitacao_id;

        $tramitacao->delete();        

        Session::flash('delete_solicitacaotramitacao', 'Tramitação excluída com sucesso!');

        return Redirect::route('solicitacoes.edit', $num_solicitacao);
    }
}
