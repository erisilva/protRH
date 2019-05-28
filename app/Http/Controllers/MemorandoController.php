<?php

namespace App\Http\Controllers;

use App\Memorando;
use App\MemorandoTipo;
use App\MemorandoSituacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

use Auth; // receber o id do operador logado no sistema

use QrCode; // desenhar barcode para consulta web

class MemorandoController extends Controller
{
    protected $pdf;

    /**
     * Construtor.
     *
     * precisa estar logado ao sistema
     * precisa ter a conta ativa (access)
     *
     * @return 
     */
    public function __construct(\App\Reports\MemorandoReport $pdf)
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);

        $this->pdf = $pdf;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::denies('setor.index')) {
            abort(403, 'Acesso negado.');
        }

        $memorandos = new Memorando;

        // filtros


        // ordena
        $memorandos = $memorandos->orderBy('id', 'desc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $memorandos = $memorandos->paginate(session('perPage', '5'));

        // tabelas auxiliares usadas pelo filtro
        $memorandotipos = MemorandoTipo::orderBy('descricao', 'asc')->get();

        $memorandosituacoes = MemorandoSituacao::orderBy('descricao', 'asc')->get();

        return view('memorandos.index', compact('memorandos', 'perpages', 'memorandotipos', 'memorandosituacoes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('setor.create')) {
            abort(403, 'Acesso negado.');
        }

        $memorandotipos = MemorandoTipo::orderBy('descricao', 'asc')->get();

        $memorandosituacoes = MemorandoSituacao::orderBy('descricao', 'asc')->get();

        return view('memorandos.create', compact('memorandotipos', 'memorandosituacoes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // geração de uma string aleatória de tamanho configurável
        function generateRandomString($length = 10) {
            return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
        }

        $memorando_input = $request->all();

        // gera uma chave aleatória de 20 caracteres
        $memorando_input['chave'] = generateRandomString(20);

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $memorando_input['user_id'] = $user->id;

        $this->validate($request, [
          'remetente' => 'required',
          'numero' => 'required',
          'memorando_tipo_id' => 'required',
          'memorando_situacao_id' => 'required',
        ],
        [
            'remetente.required' => 'Preencha o campo de remetente(s)',
            'numero.required' => 'Preencha o campo com a numeração do memorando',
            'memorando_tipo_id.required' => 'Selecione o tipo do memorando',
            'memorando_situacao_id.required' => 'Selecione a situação do memorando',
        ]);

                // salvar o barcode
        $urlImageFile = public_path() . '\qrcodes\\' . $memorando_input['chave'] . '.png';
        $urlLinkPublic = $request->url() . '/memorandos/' . $memorando_input['chave'] . '/buscar';

        // salva a imagem com o barcode
        QrCode::format('png')->size(250)->margin(1)->generate($urlLinkPublic, $urlImageFile);

        $memorando = Memorando::create($memorando_input); //salva

        #mudar aqui

        Session::flash('create_memorando', 'Memorando Nº ' . $memorando->id . ' cadastrado com sucesso!');

        return redirect(route('memorandos.index'));
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
        //
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
        //
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
