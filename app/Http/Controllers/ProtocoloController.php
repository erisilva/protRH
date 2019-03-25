<?php

namespace App\Http\Controllers;

use App\Protocolo;
use App\ProtocoloSituacao;
use App\ProtocoloTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

use Auth;

class ProtocoloController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $protocolos = new Protocolo;

        // ordena
        $protocolos = $protocolos->orderBy('id', 'desc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // consulta a tabela das situações dos protocolos
        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        // consulta a tabela dos tipos de protocolo
        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        // paginação
        $protocolos = $protocolos->paginate(session('perPage', '5'));

        return view('protocolos.index', compact('protocolos', 'perpages', 'protocolosituacoes', 'protocolotipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();


        return view('protocolos.create', compact('protocolosituacoes', 'protocolotipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        function generateRandomString($length = 10) {
            return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
        }

        $protocolo_input = $request->all();

        $protocolo_input['chave'] = generateRandomString(20);

        $user = Auth::user();

        $protocolo_input['user_id'] = $user->id;

        $this->validate($request, [
          'funcionario_id' => 'required',
          'setor_id' => 'required',
          'protocolo_tipo_id' => 'required',
          'protocolo_situacao_id' => 'required',
        ]);

        $protocolo = Protocolo::create($protocolo_input); //salva

        Session::flash('create_protocolo', 'Protocolo nº ' . $protocolo->id . ' cadastrado com sucesso!');

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        return view('protocolos.edit', compact('protocolo', 'protocolosituacoes', 'protocolotipos'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $protocolo = Protocolo::findOrFail($id);

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();        

        return view('protocolos.edit', compact('protocolo', 'protocolosituacoes', 'protocolotipos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'funcionario_id' => 'required',
          'setor_id' => 'required',
          'protocolo_tipo_id' => 'required',
          'protocolo_situacao_id' => 'required',
        ]);

        $protocolo = Protocolo::findOrFail($id);
            
        $protocolo->update($request->all());
        
        Session::flash('edited_protocolo', 'Protocolo n° ' . $protocolo->id . ' alterado com sucesso!');

        return redirect(route('protocolos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
