<?php

namespace App\Http\Controllers;

use App\Solicitacao;
use App\SolicitacaoTipo;
use App\SolicitacaoSituacao;
use App\SolicitacaoTramitacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

use Auth; // receber o id do operador logado no sistema

use QrCode; // desenhar barcode para consulta web

use Carbon\Carbon; // tratamento de datas

use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

use Illuminate\Database\Eloquent\Builder; // para poder usar o whereHas nos filtros

class SolicitacaoController extends Controller
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
    public function __construct(\App\Reports\SolicitacaoReport $pdf)
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
        if (Gate::denies('oficio.index')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacoes = new Solicitacao;

        // filtros
        if (request()->has('remetente')){
            $solicitacoes = $solicitacoes->where('remetente', 'like', '%' . request('remetente') . '%');
        }

        if (request()->has('numero')){
            $solicitacoes = $solicitacoes->where('id', 'like', '%' . request('numero') . '%');
        }

        if (request()->has('operador')){ // nome do operador que fez o cadastro
            $solicitacoes = $solicitacoes->whereHas('user', function ($query) {
                                                $query->where('name', 'like', '%' . request('operador') . '%');
                                            });
        }

        if (request()->has('solicitacao_tipo_id')){
            if (request('solicitacao_tipo_id') != ""){
                $solicitacoes = $solicitacoes->where('solicitacao_tipo_id', '=', request('solicitacao_tipo_id'));
            }
        }

        if (request()->has('solicitacao_situacao_id')){
            if (request('solicitacao_situacao_id') != ""){
                $solicitacoes = $solicitacoes->where('solicitacao_situacao_id', '=', request('solicitacao_situacao_id'));
            }
        }

        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $solicitacoes = $solicitacoes->where('created_at', '>=', $dataFormatadaMysql);                
             }
        }

        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $solicitacoes = $solicitacoes->where('created_at', '<=', $dataFormatadaMysql);                
             }
        }

        // ordena
        $solicitacoes = $solicitacoes->orderBy('id', 'desc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $solicitacoes = $solicitacoes->paginate(session('perPage', '5'))->appends([          
            'remetente' => request('remetente'),
            'numero' => request('numero'),
            'operador' => request('operador'),
            'solicitacao_tipo_id' => request('solicitacao_tipo_id'),
            'solicitacao_situacao_id' => request('solicitacao_situacao_id'),
            'dtainicio' => request('dtainicio'),
            'dtafinal' => request('dtafinal'),          
            ]);

        // tabelas auxiliares usadas pelo filtro
        $solicitacaotipos = SolicitacaoTipo::orderBy('descricao', 'asc')->get();

        $solicitacaosituacoes = SolicitacaoSituacao::orderBy('descricao', 'asc')->get();

        return view('solicitacoes.index', compact('solicitacoes', 'perpages', 'solicitacaotipos', 'solicitacaosituacoes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('oficio.create')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaotipos = SolicitacaoTipo::orderBy('descricao', 'asc')->get();

        $solicitacaosituacoes = SolicitacaoSituacao::orderBy('descricao', 'asc')->get();

        return view('solicitacoes.create', compact('solicitacaotipos', 'solicitacaosituacoes'));
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

        $solicitacao_input = $request->all();

        // gera uma chave aleatória de 20 caracteres
        $solicitacao_input['chave'] = generateRandomString(20);

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $solicitacao_input['user_id'] = $user->id;

        $this->validate($request, [
          'remetente' => 'required',
          'solicitacao_tipo_id' => 'required',
          'solicitacao_situacao_id' => 'required',
        ],
        [
            'remetente.required' => 'Preencha o campo de remetente(s)',
            'solicitacao_tipo_id.required' => 'Selecione o tipo de solicitação',
            'solicitacao_situacao_id.required' => 'Selecione a situação de solicitação',
        ]);

        // salvar o barcode
        $urlImageFile = public_path() . '\qrcodes\\' . $solicitacao_input['chave'] . '.png';
        $urlLinkPublic = $request->url() . '/' . $solicitacao_input['chave'] . '/buscar';

        // salva a imagem com o barcode
        QrCode::format('png')->size(250)->margin(1)->generate($urlLinkPublic, $urlImageFile);

        $solicitacao = Solicitacao::create($solicitacao_input); //salva

        #mudar aqui

        Session::flash('create_solicitacao', 'Solicitação Nº ' . $solicitacao->id . ' cadastrado com sucesso!');

        return Redirect::route('solicitacoes.edit', $solicitacao->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('oficio.show')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacao = Solicitacao::findOrFail($id);

        $tramitacoes = SolicitacaoTramitacao::where('solicitacao_id', '=', $id)->orderBy('id', 'desc')->get();

        return view('solicitacoes.show', compact('solicitacao', 'tramitacoes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('oficio.edit')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacao = Solicitacao::findOrFail($id);

        $solicitacaotramitacoes = SolicitacaoTramitacao::where('solicitacao_id', '=', $id)->orderBy('id', 'desc')->get();

        $solicitacaotipos = SolicitacaoTipo::orderBy('descricao', 'asc')->get();

        $solicitacaosituacoes = SolicitacaoSituacao::orderBy('descricao', 'asc')->get();

        return view('solicitacoes.edit', compact('solicitacao', 'solicitacaotipos', 'solicitacaosituacoes', 'solicitacaotramitacoes'));
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
            'remetente' => 'required',
            'solicitacao_tipo_id' => 'required',
            'solicitacao_situacao_id' => 'required',
        ],
        [
            'remetente.required' => 'Preencha o campo de remetente(s)',
            'solicitacao_tipo_id.required' => 'Selecione o tipo de solicitação',
            'solicitacao_situacao_id.required' => 'Selecione a situação de solicitação',
        ]);

        $solicitacao = Solicitacao::findOrFail($id);
            
        $solicitacao->update($request->all());
        
        Session::flash('edited_solicitacao', 'Solicitação n° ' . $solicitacao->id . ' alterada com sucesso!');

        return redirect(route('solicitacoes.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('oficio.delete')) {
            abort(403, 'Acesso negado.');
        }

        Solicitacao::findOrFail($id)->delete();

        Session::flash('deleted_solicitacao', 'Solicitação excluída com sucesso!');

        return redirect(route('solicitacoes.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('oficio.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Solicitações_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $solicitacoes = DB::table('solicitacaos');
        // joins
        $solicitacoes = $solicitacoes->join('solicitacao_tipos', 'solicitacao_tipos.id', '=', 'solicitacaos.solicitacao_tipo_id');
        $solicitacoes = $solicitacoes->join('oficio_situacaos', 'oficio_situacaos.id', '=', 'solicitacaos.solicitacao_situacao_id');
        $oficios = $solicitacoes->join('users', 'users.id', '=', 'solicitacaos.user_id');
        // select
        $solicitacoes = $solicitacoes->select('solicitacaos.id as numeroRH', DB::raw('DATE_FORMAT(solicitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(solicitacaos.created_at, \'%H:%i\') AS hora'),'solicitacaos.remetente', 'solicitacaos.identificacao','solicitacao_tipos.descricao as tipo_solicitacao', 'oficio_situacaos.descricao as situacao_solicitacao', 'solicitacaos.observacao', 'users.name as operador');
        // filtros
        if (request()->has('numeromemorando')){
            $solicitacoes = $solicitacoes->where('solicitacaos.id', 'like', '%' . request('numeromemorando') . '%');
        }
        if (request()->has('remetente')){
            $solicitacoes = $solicitacoes->where('solicitacaos.remetente', 'like', '%' . request('remetente') . '%');
        }
        if (request()->has('operador')){
            $solicitacoes = $solicitacoes->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('solicitacao_situacao_id')){
            if (request('solicitacao_situacao_id') != ""){
                $solicitacoes = $solicitacoes->where('solicitacaos.solicitacao_situacao_id', '=', request('solicitacao_situacao_id'));
            }
        }
        if (request()->has('solicitacao_tipo_id')){
            if (request('solicitacao_tipo_id') != ""){
                $solicitacoes = $solicitacoes->where('solicitacaos.solicitacao_tipo_id', '=', request('solicitacao_tipo_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $solicitacoes = $solicitacoes->where('solicitacaos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $solicitacoes = $solicitacoes->where('solicitacaos.created_at', '<=', $dataFormatadaMysql);                
             }
        }
        $solicitacoes = $solicitacoes->orderBy('solicitacaos.id', 'desc');

        $list = $solicitacoes->get()->toArray();

        # converte os objetos para uma array
        $list = json_decode(json_encode($list), true);

        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

       $callback = function() use ($list)
        {
            $FH = fopen('php://output', 'w');
            fputs($FH, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
            foreach ($list as $row) {
                fputcsv($FH, $row, chr(9));
            }
            fclose($FH);
        };

        return Response::stream($callback, 200, $headers);
    }    
}
