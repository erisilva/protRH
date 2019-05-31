<?php

namespace App\Http\Controllers;

use App\MemorandoTramitacao;
use App\Memorando;
use App\MemorandoSituacao;
use App\MemorandoTipo;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon; // tratamento de datas
use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

use Auth; // receber o id do operador logado no sistema

class MemorandoTramitacaoController extends Controller
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
        if (Gate::denies('memorando.tramitacao.create')) {
            abort(403, 'Acesso negado.');
        }

        $input_tramitacao = $request->all();

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $input_tramitacao['user_id'] = $user->id;

        // ajusta os parametros (nomes) da entrada
        $input_tramitacao['setor_id'] = $input_tramitacao['setor_tramitacao_id'];
        $input_tramitacao['funcionario_id'] = $input_tramitacao['funcionario_tramitacao_id'];

        MemorandoTramitacao::create($input_tramitacao); //salva

        Session::flash('create_memorandotramitacao', 'Tramitação inserida com sucesso!');

        return Redirect::route('memorandos.edit', $input_tramitacao['memorando_id']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('memorando.tramitacao.delete')) {
            abort(403, 'Acesso negado.');
        }
        
        $tramitacao = MemorandoTramitacao::findOrFail($id);

        $num_memorando = $tramitacao->memorando_id;

        $tramitacao->delete();        

        Session::flash('delete_memorandotramitacao', 'Tramitação excluída com sucesso!');

        return Redirect::route('memorandos.edit', $num_memorando);
    }
}
