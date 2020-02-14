<?php

namespace App\Http\Controllers;

use App\Protocolo;
use App\Tramitacao;
use App\Periodo;
use App\ProtocoloSituacao;
use App\ProtocoloTipo;
use App\PeriodoTipo;
use App\Grupo;
use App\Resposta;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Builder; // para poder usar o whereHas nos filtros

use Auth; // receber o id do operador logado no sistema

use Carbon\Carbon; // tratamento de datas

use QrCode;

class ProtocoloController extends Controller
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
    public function __construct(\App\Reports\ProtocoloReport $pdf)
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
        if (Gate::denies('protocolo.index')) {
            abort(403, 'Acesso negado.');
        }
        $protocolos = new Protocolo;

        // filtros
        if (request()->has('numprotocolo')){
            $protocolos = $protocolos->where('id', 'like', '%' . request('numprotocolo') . '%');
        }

        if (request()->has('nome')){ // nome do funcionário
            $protocolos = $protocolos->whereHas('funcionario', function ($query) {
                                                $query->where('nome', 'like', '%' . request('nome') . '%');
                                            });
        }

        if (request()->has('setor')){ // nome do setor
            $protocolos = $protocolos->whereHas('setor', function ($query) {
                                                $query->where('descricao', 'like', '%' . request('setor') . '%');
                                            });
        }

        if (request()->has('operador')){ // nome do operador
            $protocolos = $protocolos->whereHas('user', function ($query) {
                                                $query->where('name', 'like', '%' . request('operador') . '%');
                                            });
        }

        if (request()->has('protocolo_tipo_id')){
            if (request('protocolo_tipo_id') != ""){
                $protocolos = $protocolos->where('protocolo_tipo_id', '=', request('protocolo_tipo_id'));
            }
        } 

        if (request()->has('protocolo_situacao_id')){
            if (request('protocolo_situacao_id') != ""){
                $protocolos = $protocolos->where('protocolo_situacao_id', '=', request('protocolo_situacao_id'));
            }
        }

        if (request()->has('protocolo_grupo_id')){
            if (request('protocolo_grupo_id') != ""){
                $protocolos = $protocolos->where('grupo_id', '=', request('protocolo_grupo_id'));
            }
        } 

        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $protocolos = $protocolos->where('created_at', '>=', $dataFormatadaMysql);                
             }
        }

        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $protocolos = $protocolos->where('created_at', '<=', $dataFormatadaMysql);                
             }
        }

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

        // consulta a tabela dos tipos de protocolo
        $protocologrupos = Grupo::orderBy('descricao', 'asc')->get();

        // paginação
        $protocolos = $protocolos->paginate(session('perPage', '5'))->appends([          
            'numprotocolo' => request('numprotocolo'),
            'nome' => request('nome'),
            'setor' => request('setor'),
            'operador' => request('operador'),
            'protocolo_tipo_id' => request('protocolo_tipo_id'),
            'protocolo_situacao_id' => request('protocolo_situacao_id'),
            'dtainicio' => request('dtainicio'),
            'dtafinal' => request('dtafinal'),          
            ]);

        return view('protocolos.index', compact('protocolos', 'perpages', 'protocolosituacoes', 'protocolotipos', 'protocologrupos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('protocolo.create')) {
            abort(403, 'Acesso negado.');
        }

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        return view('protocolos.create', compact('protocolotipos'));
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

        $protocolo_input = $request->all();

        // gera uma chave aleatória de 20 caracteres
        $protocolo_input['chave'] = generateRandomString(20);

        // grupo de trabalho padrão
        $protocolo_input['grupo_id'] = 1; // não encaminhado para nenhuma grupo

        // dados da conclusão
        $protocolo_input['concluido'] = 'n'; // ainda não foi concluido
        $protocolo_input['resposta_id'] = 1; // ainda não disponível

        // situação do protocolo
        $protocolo_input['protocolo_situacao_id'] = 1;

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $protocolo_input['user_id'] = $user->id;

        $this->validate($request, [
          'funcionario_id' => 'required',
          'setor_id' => 'required',
          'protocolo_tipo_id' => 'required',
        ],
        [
            'funcionario_id.required' => 'Selecione um funcionário para o protocolo',
            'setor_id.required' => 'Selecione o setor para esse protocolo',
            'protocolo_tipo_id.required' => 'Selecione o tipo do protocolo',
        ]);

        // salvar o barcode
        $urlImageFile = public_path() . '\qrcodes\\' . $protocolo_input['chave'] . '.png';
        $urlLinkPublic = $request->url() . '/' . $protocolo_input['chave'] . '/buscar';

        // salva a imagem com o barcode
        QrCode::format('png')->size(250)->margin(1)->generate($urlLinkPublic, $urlImageFile);

        $protocolo = Protocolo::create($protocolo_input); //salva

        Session::flash('create_protocolo', 'Protocolo nº ' . $protocolo->id . ' cadastrado com sucesso!');

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $protocolo->id)->orderBy('id', 'desc')->get();

        $periodos = Periodo::where('protocolo_id', '=', $protocolo->id)->orderBy('id', 'asc')->get();

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();
        
        //return view('protocolos.edit', compact('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos'));

        return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos', 'tramitacoes');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('protocolo.show')) {
            abort(403, 'Acesso negado.');
        }

        $protocolo = Protocolo::findOrFail($id);

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $id)->orderBy('id', 'desc')->get();

        $periodos = Periodo::where('protocolo_id', '=', $id)->orderBy('id', 'asc')->get();

        return view('protocolos.show', compact('protocolo', 'tramitacoes', 'periodos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('protocolo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $protocolo = Protocolo::findOrFail($id);

        $tramitacoes = Tramitacao::where('protocolo_id', '=', $id)->orderBy('id', 'desc')->get();

        $periodos = Periodo::where('protocolo_id', '=', $id)->orderBy('id', 'asc')->get();

        $anexos = $protocolo->anexos()->orderBy('id', 'desc')->get();

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get(); 

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();      
        
        $grupos = Grupo::orderBy('descricao', 'asc')->get();      
        
        $respostas = Resposta::orderBy('descricao', 'asc')->get();      

        return view('protocolos.edit', compact('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos', 'tramitacoes', 'anexos', 'grupos', 'respostas'));

        //return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos');
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
          'protocolo_tipo_id' => 'required',
        ],
        [
            'protocolo_tipo_id.required' => 'Selecione o tipo do protocolo',
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
        if (Gate::denies('protocolo.delete')) {
            abort(403, 'Acesso negado.');
        }

        Protocolo::findOrFail($id)->delete();

        Session::flash('deleted_protocolo', 'Protocolo excluído com sucesso!');

        return redirect(route('protocolos.index'));
    }


    /**
     * Preenche a vaga com o funcionario selecionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function concluir(Request $request, $id)
    {
      if (Gate::denies('protocolo.concluir')) {
            abort(403, 'Acesso negado.');
      }

      $this->validate($request, [
          'resposta_id' => 'required',
        ],
        [
            'resposta_id.required' => 'Selecione a resposta da conclusão do protocolo',
        ]);

      $protocolo_input = $request->all();

      $protocolo = Protocolo::findOrFail($id);

      $protocolo->concluido_mensagem = $protocolo_input['concluido_mensagem'];

      $protocolo->concluido = 's';

      $protocolo->concluido_em = Carbon::now()->toDateTimeString();

      $protocolo->resposta_id = $protocolo_input['resposta_id'];

      $protocolo->protocolo_situacao_id = 4; // concluido

      $protocolo->save();

      return redirect(route('protocolos.edit', $id));
    }


    /**
     * Preenche a vaga com o funcionario selecionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function encaminhar(Request $request, $id)
    {
      if (Gate::denies('protocolo.encaminhar')) {
            abort(403, 'Acesso negado.');
      }

      $this->validate($request, [
          'grupo_id' => 'required',
        ],
        [
            'grupo_id.required' => 'Selecione o grupo a ser encaminhado o protocolo',
        ]);

      $protocolo_input = $request->all();

      $protocolo = Protocolo::findOrFail($id);

      $protocolo->grupo_id = $protocolo_input['grupo_id'];

      $protocolo->protocolo_situacao_id = 2; // encaminhado

      $protocolo->encaminhado_em = Carbon::now()->toDateTimeString();

      $protocolo->save();

      return redirect(route('protocolos.edit', $id));
    }  

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=protocolos_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $protocolos = DB::table('protocolos');
        // joins
        $protocolos = $protocolos->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $protocolos = $protocolos->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $protocolos = $protocolos->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $protocolos = $protocolos->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $protocolos = $protocolos->join('users', 'users.id', '=', 'protocolos.user_id');
        $protocolos = $protocolos->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        $protocolos = $protocolos->join('respostas', 'respostas.id', '=', 'protocolos.resposta_id');
        // select
        $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador', 
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'protocolos.concluido as concluido',
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'protocolos.concluido_mensagem as resposta_mensagem',
        );

        //filtros
        if (request()->has('numprotocolo')){
            $protocolos = $protocolos->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
        }
        if (request()->has('nome')){
            $protocolos = $protocolos->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
        }
        if (request()->has('operador')){
            $protocolos = $protocolos->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('setor')){
            $protocolos = $protocolos->where('setors.descricao', 'like', '%' . request('setor') . '%');
        }
        if (request()->has('protocolo_tipo_id')){
            if (request('protocolo_tipo_id') != ""){
                $protocolos = $protocolos->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
            }
        }
        if (request()->has('protocolo_situacao_id')){
            if (request('protocolo_situacao_id') != ""){
                $protocolos = $protocolos->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
            }
        }
        if (request()->has('protocolo_grupo_id')){
            if (request('protocolo_grupo_id') != ""){
                $protocolos = $protocolos->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $protocolos = $protocolos->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $protocolos = $protocolos->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
             }
        }

        $protocolos = $protocolos->orderBy('protocolos.id', 'desc');

        $list = $protocolos->get()->toArray();

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

    /**
     * Exportação para pdf
     *
     * @param  
     * @return 
     */
    public function exportpdf()
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }

        // consulta principal
        $protocolos = DB::table('protocolos');
        // joins
        $protocolos = $protocolos->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $protocolos = $protocolos->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $protocolos = $protocolos->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $protocolos = $protocolos->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $protocolos = $protocolos->join('users', 'users.id', '=', 'protocolos.user_id');
        $protocolos = $protocolos->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        $protocolos = $protocolos->join('respostas', 'respostas.id', '=', 'protocolos.resposta_id');
        // select
        $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador',
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'protocolos.concluido as concluido',
          'protocolos.grupo_id as grupo_id',
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'protocolos.concluido_mensagem as resposta_mensagem',
        );

        //filtros
        if (request()->has('numprotocolo')){
            $protocolos = $protocolos->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
        }
        if (request()->has('nome')){
            $protocolos = $protocolos->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
        }
        if (request()->has('setor')){
            $protocolos = $protocolos->where('setors.descricao', 'like', '%' . request('setor') . '%');
        }
        if (request()->has('operador')){
            $protocolos = $protocolos->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('protocolo_tipo_id')){
            if (request('protocolo_tipo_id') != ""){
                $protocolos = $protocolos->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
            }
        }
        if (request()->has('protocolo_situacao_id')){
            if (request('protocolo_situacao_id') != ""){
                $protocolos = $protocolos->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
            }
        }
        if (request()->has('protocolo_grupo_id')){
            if (request('protocolo_grupo_id') != ""){
                $protocolos = $protocolos->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $protocolos = $protocolos->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $protocolos = $protocolos->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
             }
        }

        $protocolos = $protocolos->orderBy('protocolos.id', 'desc');

        $protocolos = $protocolos->get();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        foreach ($protocolos as $protocolo) {
            $this->pdf->Cell(40, 6, utf8_decode('Número'), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(40, 6, utf8_decode($protocolo->numero), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode($protocolo->data), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode($protocolo->hora), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode($protocolo->operador), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(46, 6, utf8_decode('Matrícula'), 1, 0,'L');
            $this->pdf->Cell(140, 6, utf8_decode('Funcionário'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(46, 6, utf8_decode($protocolo->matricula), 1, 0,'L');
            $this->pdf->Cell(140, 6, utf8_decode($protocolo->funcionario), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(46, 6, utf8_decode('Código'), 1, 0,'L');
            $this->pdf->Cell(140, 6, utf8_decode('Setor'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(46, 6, utf8_decode($protocolo->codigo_setor), 1, 0,'L');
            $this->pdf->Cell(140, 6, utf8_decode($protocolo->setor), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(110, 6, utf8_decode('Tipo'), 1, 0,'L');
            $this->pdf->Cell(76, 6, utf8_decode('Situação'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(110, 6, utf8_decode($protocolo->tipo_protocolo), 1, 0,'L');
            $this->pdf->Cell(76, 6, utf8_decode($protocolo->situacao_protocolo), 1, 0,'L');
            $this->pdf->Ln();
            if ($protocolo->observacoes != ''){
              $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->observacoes), 1, 'L', false);
            }
            if ($protocolo->grupo_id > 1){
              $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->Cell(126, 6, utf8_decode($protocolo->encaminhado_para), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_encaminhamento), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_encaminhamento), 1, 0,'L');
              $this->pdf->Ln();
            }
            if ($protocolo->concluido == 's'){
              $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->Cell(126, 6, utf8_decode($protocolo->resposta), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_conclusao), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_conclusao), 1, 0,'L');
              $this->pdf->Ln();
              if ($protocolo->resposta_mensagem != ''){
                $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->resposta_mensagem), 1, 'L', false);
              }
            }
            // periodos
            // consulta secundaria
            $periodos = DB::table('periodos');
            // joins
            $periodos = $periodos->join('periodo_tipos', 'periodo_tipos.id', '=', 'periodos.periodo_tipo_id');
            // select
            $periodos = $periodos->select(DB::raw('DATE_FORMAT(periodos.inicio, \'%d/%m/%Y\') AS datainicio'), DB::raw('DATE_FORMAT(periodos.fim, \'%d/%m/%Y\') AS datafim'), 'periodo_tipos.descricao as tipo' );
            // filter
            $periodos = $periodos->where('periodos.protocolo_id', '=', $protocolo->numero);
            // get
            $periodos = $periodos->get();

            if (count($periodos)){
                $this->pdf->Cell(186, 6, utf8_decode('Períodos'), 'B', 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(40, 6, utf8_decode('Data inicial'), 0, 0,'L');
                $this->pdf->Cell(40, 6, utf8_decode('Data Final'), 0, 0,'L');
                $this->pdf->Cell(106, 6, utf8_decode('Descrição'), 0, 0,'L');
                $this->pdf->Ln();
                foreach ($periodos as $periodo) {
                    $this->pdf->Cell(40, 6, utf8_decode($periodo->datainicio ?? '-'), 0, 0,'L');
                    $this->pdf->Cell(40, 6, utf8_decode($periodo->datafim ?? '-'), 0, 0,'L');
                    $this->pdf->Cell(106, 6, utf8_decode($periodo->tipo), 0, 0,'L');
                    $this->pdf->Ln();
                } 
            }

            // tramitações
            // consulta secundariatramitacoes
            $tramitacoes = DB::table('tramitacaos');
            // joins
            $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'tramitacaos.funcionario_id');
            $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'tramitacaos.setor_id');
            $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'tramitacaos.user_id');
            // select
            $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'tramitacaos.descricao as observacoes');
            // filter
            $tramitacoes = $tramitacoes->where('tramitacaos.protocolo_id', '=', $protocolo->numero);
            // ordena
            $tramitacoes = $tramitacoes->orderBy('tramitacaos.id', 'desc');
            // get
            $tramitacoes = $tramitacoes->get();
            if (count($tramitacoes)){
                $this->pdf->Cell(186, 6, utf8_decode('Tramitações'), 'B', 0,'L');
                $this->pdf->Ln();
                // diminui a fonte
                $this->pdf->SetFont('Arial', '', 10);
                foreach ($tramitacoes as $tramitacao) {
                    $this->pdf->Cell(36, 5, utf8_decode('Data: ' . $tramitacao->data), 1, 0,'L');
                    $this->pdf->Cell(20, 5, utf8_decode('Hora: ' . $tramitacao->hora), 1, 0,'L');
                    $this->pdf->Cell(130, 5, utf8_decode('Operador: ' . $tramitacao->operador), 1, 0,'L');
                    $this->pdf->Ln();
                    $this->pdf->Cell(56, 5, utf8_decode('Matrícula: ' . $tramitacao->matricula), 1, 0,'L');
                    $this->pdf->Cell(130, 5, utf8_decode('Funcionário: ' . $tramitacao->funcionario), 1, 0,'L');
                    $this->pdf->Ln();
                    $this->pdf->Cell(56, 5, utf8_decode('Código: ' . $tramitacao->codigo), 1, 0,'L');
                    $this->pdf->Cell(130, 5, utf8_decode('Setor: ' . $tramitacao->setor), 1, 0,'L');
                    $this->pdf->Ln();
                    if ($tramitacao->observacoes != ''){
                        $this->pdf->MultiCell(186, 6, utf8_decode('observações: ' . $tramitacao->observacoes), 1, 'L', false);
                    }

                    $this->pdf->Ln(2);
                }
            }

            $this->pdf->SetFont('Arial', '', 12);

            $this->pdf->Ln(2);
        }

        $this->pdf->Output('D', 'Protocolos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }

    /**
     * Exportação para pdf por protocolo
     *
     * @param  $id, id do protocolo
     * @return pdf
     */
    public function exportpdfindividual($id)
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }
        
        // consulta principal
        $protocolo = DB::table('protocolos');
        // joins
        $protocolo = $protocolo->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $protocolo = $protocolo->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $protocolo = $protocolo->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $protocolo = $protocolo->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $protocolo = $protocolo->join('users', 'users.id', '=', 'protocolos.user_id');
        $protocolo = $protocolo->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        $protocolo = $protocolo->join('respostas', 'respostas.id', '=', 'protocolos.resposta_id');
        // select
        $protocolo = $protocolo->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador', 'protocolos.chave',
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'protocolos.concluido as concluido',
          'protocolos.grupo_id as grupo_id',
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'protocolos.concluido_mensagem as resposta_mensagem',
        );

        //filtros
        $protocolo = $protocolo->where('protocolos.id', '=', $id);
        // get
        $protocolo = $protocolo->get()->first();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();
        $this->pdf->Cell(40, 6, utf8_decode('Número'), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(40, 6, utf8_decode($protocolo->numero), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode($protocolo->data), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode($protocolo->hora), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode($protocolo->operador), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(46, 6, utf8_decode('Matrícula'), 1, 0,'L');
        $this->pdf->Cell(140, 6, utf8_decode('Funcionário'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(46, 6, utf8_decode($protocolo->matricula), 1, 0,'L');
        $this->pdf->Cell(140, 6, utf8_decode($protocolo->funcionario), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(46, 6, utf8_decode('Código'), 1, 0,'L');
        $this->pdf->Cell(140, 6, utf8_decode('Setor'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(46, 6, utf8_decode($protocolo->codigo_setor), 1, 0,'L');
        $this->pdf->Cell(140, 6, utf8_decode($protocolo->setor), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(110, 6, utf8_decode('Tipo'), 1, 0,'L');
        $this->pdf->Cell(76, 6, utf8_decode('Situação'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(110, 6, utf8_decode($protocolo->tipo_protocolo), 1, 0,'L');
        $this->pdf->Cell(76, 6, utf8_decode($protocolo->situacao_protocolo), 1, 0,'L');
        $this->pdf->Ln();
        if ($protocolo->observacoes != ''){
            $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->observacoes), 1, 'L', false);
        }
        if ($protocolo->grupo_id > 1){
          $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(126, 6, utf8_decode($protocolo->encaminhado_para), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_encaminhamento), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_encaminhamento), 1, 0,'L');
          $this->pdf->Ln();
        }
        if ($protocolo->concluido == 's'){
          $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(126, 6, utf8_decode($protocolo->resposta), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_conclusao), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_conclusao), 1, 0,'L');
          $this->pdf->Ln();
          if ($protocolo->resposta_mensagem != ''){
            $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->resposta_mensagem), 1, 'L', false);
          }
        }
        // periodos
        // consulta secundaria
        $periodos = DB::table('periodos');
        // joins
        $periodos = $periodos->join('periodo_tipos', 'periodo_tipos.id', '=', 'periodos.periodo_tipo_id');
        // select
        $periodos = $periodos->select(DB::raw('DATE_FORMAT(periodos.inicio, \'%d/%m/%Y\') AS datainicio'), DB::raw('DATE_FORMAT(periodos.fim, \'%d/%m/%Y\') AS datafim'), 'periodo_tipos.descricao as tipo' );
        // filter
        $periodos = $periodos->where('periodos.protocolo_id', '=', $id);
        // get
        $periodos = $periodos->get();

        if (count($periodos)){
            $this->pdf->Cell(186, 6, utf8_decode('Períodos'), 'B', 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(40, 6, utf8_decode('Data inicial'), 0, 0,'L');
            $this->pdf->Cell(40, 6, utf8_decode('Data Final'), 0, 0,'L');
            $this->pdf->Cell(106, 6, utf8_decode('Descrição'), 0, 0,'L');
            $this->pdf->Ln();
            foreach ($periodos as $periodo) {
                $this->pdf->Cell(40, 6, utf8_decode($periodo->datainicio ?? '-'), 0, 0,'L');
                $this->pdf->Cell(40, 6, utf8_decode($periodo->datafim ?? '-'), 0, 0,'L');
                $this->pdf->Cell(106, 6, utf8_decode($periodo->tipo), 0, 0,'L');
                $this->pdf->Ln();
            } 
        }

        // tramitações
        // consulta secundariatramitacoes
        $tramitacoes = DB::table('tramitacaos');
        // joins
        $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'tramitacaos.funcionario_id');
        $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'tramitacaos.setor_id');
        $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'tramitacaos.user_id');
        // select
        $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'tramitacaos.descricao as observacoes');
        // filter
        $tramitacoes = $tramitacoes->where('tramitacaos.protocolo_id', '=', $id);
        // ordena
        $tramitacoes = $tramitacoes->orderBy('tramitacaos.id', 'desc');
        // get
        $tramitacoes = $tramitacoes->get();
        if (count($tramitacoes)){
            $this->pdf->Cell(186, 6, utf8_decode('Tramitações'), 'B', 0,'L');
            $this->pdf->Ln();
            // diminui a fonte
            $this->pdf->SetFont('Arial', '', 10);
            foreach ($tramitacoes as $tramitacao) {
                $this->pdf->Cell(36, 5, utf8_decode('Data: ' . $tramitacao->data), 1, 0,'L');
                $this->pdf->Cell(20, 5, utf8_decode('Hora: ' . $tramitacao->hora), 1, 0,'L');
                $this->pdf->Cell(130, 5, utf8_decode('Operador: ' . $tramitacao->operador), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(56, 5, utf8_decode('Matrícula: ' . $tramitacao->matricula), 1, 0,'L');
                $this->pdf->Cell(130, 5, utf8_decode('Funcionário: ' . $tramitacao->funcionario), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(56, 5, utf8_decode('Código: ' . $tramitacao->codigo), 1, 0,'L');
                $this->pdf->Cell(130, 5, utf8_decode('Setor: ' . $tramitacao->setor), 1, 0,'L');
                $this->pdf->Ln();
                if ($tramitacao->observacoes != ''){
                    $this->pdf->MultiCell(186, 6, utf8_decode('observações: ' . $tramitacao->observacoes), 1, 'L', false);
                }

                $this->pdf->Ln(2);
            }
        }

        $this->pdf->Ln(2);

        // imprime o barcode
        $urlLinkPublic = 'qrcodes/' . $protocolo->chave . '.png';

        //$urlImageFile = public_path() . '\qrcodes\\' . $protocolo->chave . '.png';



        //$this->pdf->Cell(186, 5, utf8_decode('link: ' . $urlLinkPublic), 1, 0,'L');

        //dd(response()->file($urlImageFile));

        $this->pdf->Image($urlLinkPublic, null, null, 0, 0, 'PNG');

        $this->pdf->Ln(2);

        $this->pdf->Output('D', 'Protocolo_num' . $id . '_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }

    /**
     * Exportação para pdf por setor (completo)
     *
     * @param  
     * @return pdf
     */
    public function exportpdfporsetor()
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }

        // busca os setores através do group by, de 
        // acordo com os filtros

        // consulta principal
        $setores = DB::table('protocolos');
        // joins
        $setores = $setores->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $setores = $setores->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $setores = $setores->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $setores = $setores->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $setores = $setores->join('users', 'users.id', '=', 'protocolos.user_id');
        $setores = $setores->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        // select
        $setores = $setores->select('setors.id', 'setors.descricao');

        //filtros
        if (request()->has('numprotocolo')){
            $setores = $setores->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
        }
        if (request()->has('nome')){
            $setores = $setores->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
        }
        if (request()->has('setor')){
            $setores = $setores->where('setors.descricao', 'like', '%' . request('setor') . '%');
        }
        if (request()->has('operador')){
            $setores = $setores->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('protocolo_tipo_id')){
            if (request('protocolo_tipo_id') != ""){
                $setores = $setores->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
            }
        }
        if (request()->has('protocolo_situacao_id')){
            if (request('protocolo_situacao_id') != ""){
                $setores = $setores->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
            }
        }
        if (request()->has('protocolo_grupo_id')){
            if (request('protocolo_grupo_id') != ""){
                $setores = $setores->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
            }
        }
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $setores = $setores->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $setores = $setores->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
             }
        }

        // group by
        $setores = $setores->groupBy('setors.id', 'setors.descricao');

        $setores = $setores->orderBy('setors.descricao', 'asc');

        $setores = $setores->get();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);

        foreach ($setores as $setor) {
            $this->pdf->AddPage();

            // subtitulo
            $this->pdf->SetFillColor(100);
            $this->pdf->SetTextColor(0);
            $this->pdf->SetDrawColor(0);
            $this->pdf->SetFont('Arial','',14);
            $this->pdf->Cell(186, 8, utf8_decode('Setor: ' . $setor->descricao), 1, 1,'L', 1);
            $this->pdf->Ln(2);

            // busca e imprime os protocolos para cada setor
            $protocolos = DB::table('protocolos');
            // joins
            $protocolos = $protocolos->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
            $protocolos = $protocolos->join('setors', 'setors.id', '=', 'protocolos.setor_id');
            $protocolos = $protocolos->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
            $protocolos = $protocolos->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
            $protocolos = $protocolos->join('users', 'users.id', '=', 'protocolos.user_id');
            $protocolos = $protocolos->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
            $protocolos = $protocolos->join('respostas', 'respostas.id', '=', 'protocolos.resposta_id');
            // select
            $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador',

              DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
              DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
              DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
              'protocolos.concluido as concluido',
              'protocolos.grupo_id as grupo_id',
              DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
              DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%H:%i\') AS hora_conclusao'),
              DB::raw("coalesce(respostas.descricao, '-') as resposta"),
              'protocolos.concluido_mensagem as resposta_mensagem',

            );

            //filtros
            $protocolos = $protocolos->where('protocolos.setor_id', '=', $setor->id); // filtro principal

            if (request()->has('numprotocolo')){
                $protocolos = $protocolos->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
            }
            if (request()->has('nome')){
                $protocolos = $protocolos->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
            }
            if (request()->has('setor')){
                $protocolos = $protocolos->where('setors.descricao', 'like', '%' . request('setor') . '%');
            }
            if (request()->has('operador')){
                $protocolos = $protocolos->where('users.name', 'like', '%' . request('operador') . '%');
            }
            if (request()->has('protocolo_tipo_id')){
                if (request('protocolo_tipo_id') != ""){
                    $protocolos = $protocolos->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
                }
            }
            if (request()->has('protocolo_situacao_id')){
                if (request('protocolo_situacao_id') != ""){
                    $protocolos = $protocolos->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
                }
            }
            if (request()->has('protocolo_grupo_id')){
                if (request('protocolo_grupo_id') != ""){
                    $protocolos = $protocolos->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
                }
            }
            if (request()->has('dtainicio')){
                 if (request('dtainicio') != ""){
                    $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                    $protocolos = $protocolos->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
                 }
            }
            if (request()->has('dtafinal')){
                 if (request('dtafinal') != ""){
                    // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                    $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                    $protocolos = $protocolos->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
                 }
            }

            $protocolos = $protocolos->orderBy('protocolos.id', 'desc');

            $protocolos = $protocolos->get();

            foreach ($protocolos as $protocolo) {
                $this->pdf->SetFont('Arial', '', 12);
                
                $this->pdf->Cell(40, 6, utf8_decode('Número'), 1, 0,'R');
                $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
                $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
                $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(40, 6, utf8_decode($protocolo->numero), 1, 0,'R');
                $this->pdf->Cell(30, 6, utf8_decode($protocolo->data), 1, 0,'L');
                $this->pdf->Cell(26, 6, utf8_decode($protocolo->hora), 1, 0,'L');
                $this->pdf->Cell(90, 6, utf8_decode($protocolo->operador), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(46, 6, utf8_decode('Matrícula'), 1, 0,'L');
                $this->pdf->Cell(140, 6, utf8_decode('Funcionário'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(46, 6, utf8_decode($protocolo->matricula), 1, 0,'L');
                $this->pdf->Cell(140, 6, utf8_decode($protocolo->funcionario), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(110, 6, utf8_decode('Tipo'), 1, 0,'L');
                $this->pdf->Cell(76, 6, utf8_decode('Situação'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->Cell(110, 6, utf8_decode($protocolo->tipo_protocolo), 1, 0,'L');
                $this->pdf->Cell(76, 6, utf8_decode($protocolo->situacao_protocolo), 1, 0,'L');
                $this->pdf->Ln();
                if ($protocolo->observacoes != ''){
                    $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
                    $this->pdf->Ln();
                    $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->observacoes), 1, 'L', false);
                }
                if ($protocolo->grupo_id > 1){
                  $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
                  $this->pdf->Ln();
                  $this->pdf->Cell(126, 6, utf8_decode($protocolo->encaminhado_para), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_encaminhamento), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_encaminhamento), 1, 0,'L');
                  $this->pdf->Ln();
                }
                if ($protocolo->concluido == 's'){
                  $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
                  $this->pdf->Ln();
                  $this->pdf->Cell(126, 6, utf8_decode($protocolo->resposta), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_conclusao), 1, 0,'L');
                  $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_conclusao), 1, 0,'L');
                  $this->pdf->Ln();
                  if ($protocolo->resposta_mensagem != ''){
                    $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
                    $this->pdf->Ln();
                    $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->resposta_mensagem), 1, 'L', false);
                  }
                }  
                // periodos
                // consulta secundaria
                $periodos = DB::table('periodos');
                // joins
                $periodos = $periodos->join('periodo_tipos', 'periodo_tipos.id', '=', 'periodos.periodo_tipo_id');
                // select
                $periodos = $periodos->select(DB::raw('DATE_FORMAT(periodos.inicio, \'%d/%m/%Y\') AS datainicio'), DB::raw('DATE_FORMAT(periodos.fim, \'%d/%m/%Y\') AS datafim'), 'periodo_tipos.descricao as tipo' );
                // filter
                $periodos = $periodos->where('periodos.protocolo_id', '=', $protocolo->numero);
                // get
                $periodos = $periodos->get();

                if (count($periodos)){
                    $this->pdf->Cell(186, 6, utf8_decode('Períodos'), 'B', 0,'L');
                    $this->pdf->Ln();
                    $this->pdf->Cell(40, 6, utf8_decode('Data inicial'), 0, 0,'L');
                    $this->pdf->Cell(40, 6, utf8_decode('Data Final'), 0, 0,'L');
                    $this->pdf->Cell(106, 6, utf8_decode('Descrição'), 0, 0,'L');
                    $this->pdf->Ln();
                    foreach ($periodos as $periodo) {
                        $this->pdf->Cell(40, 6, utf8_decode($periodo->datainicio ?? '-'), 0, 0,'L');
                        $this->pdf->Cell(40, 6, utf8_decode($periodo->datafim ?? '-'), 0, 0,'L');
                        $this->pdf->Cell(106, 6, utf8_decode($periodo->tipo), 0, 0,'L');
                        $this->pdf->Ln();
                    } 
                }

                // tramitações
                // consulta secundariatramitacoes
                $tramitacoes = DB::table('tramitacaos');
                // joins
                $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'tramitacaos.funcionario_id');
                $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'tramitacaos.setor_id');
                $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'tramitacaos.user_id');
                // select
                $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'tramitacaos.descricao as observacoes');
                // filter
                $tramitacoes = $tramitacoes->where('tramitacaos.protocolo_id', '=', $protocolo->numero);
                // ordena
                $tramitacoes = $tramitacoes->orderBy('tramitacaos.id', 'desc');
                // get
                $tramitacoes = $tramitacoes->get();
                if (count($tramitacoes)){
                    $this->pdf->Cell(186, 6, utf8_decode('Tramitações'), 'B', 0,'L');
                    $this->pdf->Ln();
                    // diminui a fonte
                    $this->pdf->SetFont('Arial', '', 10);
                    foreach ($tramitacoes as $tramitacao) {
                        $this->pdf->Cell(36, 5, utf8_decode('Data: ' . $tramitacao->data), 1, 0,'L');
                        $this->pdf->Cell(20, 5, utf8_decode('Hora: ' . $tramitacao->hora), 1, 0,'L');
                        $this->pdf->Cell(130, 5, utf8_decode('Operador: ' . $tramitacao->operador), 1, 0,'L');
                        $this->pdf->Ln();
                        $this->pdf->Cell(56, 5, utf8_decode('Matrícula: ' . $tramitacao->matricula), 1, 0,'L');
                        $this->pdf->Cell(130, 5, utf8_decode('Funcionário: ' . $tramitacao->funcionario), 1, 0,'L');
                        $this->pdf->Ln();
                        $this->pdf->Cell(56, 5, utf8_decode('Código: ' . $tramitacao->codigo), 1, 0,'L');
                        $this->pdf->Cell(130, 5, utf8_decode('Setor: ' . $tramitacao->setor), 1, 0,'L');
                        $this->pdf->Ln();
                        if ($tramitacao->observacoes != ''){
                            $this->pdf->MultiCell(186, 6, utf8_decode('observações: ' . $tramitacao->observacoes), 1, 'L', false);
                        }

                        $this->pdf->Ln(2);
                    }
                }

                $this->pdf->Ln(2);
            }

        }

        $this->pdf->Output('D', 'Protocolos_por_setor_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;       
    }

    /**
     * Exportação para pdf por setor (simples)
     *
     * @param  
     * @return pdf
     */
    public function exportpdfporsetorsimples()
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }

        // busca os setores através do group by, de 
        // acordo com os filtros

        // consulta principal
        $setores = DB::table('protocolos');
        // joins
        $setores = $setores->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $setores = $setores->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $setores = $setores->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $setores = $setores->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $setores = $setores->join('users', 'users.id', '=', 'protocolos.user_id');
        $setores = $setores->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        // select
        $setores = $setores->select('setors.id', 'setors.descricao');

        //filtros
        if (request()->has('numprotocolo')){
            $setores = $setores->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
        }
        if (request()->has('nome')){
            $setores = $setores->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
        }
        if (request()->has('setor')){
            $setores = $setores->where('setors.descricao', 'like', '%' . request('setor') . '%');
        }
        if (request()->has('operador')){
            $setores = $setores->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('protocolo_tipo_id')){
            if (request('protocolo_tipo_id') != ""){
                $setores = $setores->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
            }
        }
        if (request()->has('protocolo_situacao_id')){
            if (request('protocolo_situacao_id') != ""){
                $setores = $setores->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
            }
        }
        if (request()->has('protocolo_grupo_id')){
            if (request('protocolo_grupo_id') != ""){
                $setores = $setores->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
            }
        }
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $setores = $setores->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $setores = $setores->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
             }
        }

        // group by
        $setores = $setores->groupBy('setors.id', 'setors.descricao');

        $setores = $setores->orderBy('setors.descricao', 'asc');

        $setores = $setores->get();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);

        foreach ($setores as $setor) {
            $this->pdf->AddPage();

            // subtitulo
            $this->pdf->SetFillColor(100);
            $this->pdf->SetTextColor(0);
            $this->pdf->SetDrawColor(0);
            $this->pdf->SetFont('Arial','',14);
            $this->pdf->Cell(186, 8, utf8_decode('Setor: ' . $setor->descricao), 1, 1,'L', 1);
            $this->pdf->Ln(2);

            // busca e imprime os protocolos para cada setor
            $protocolos = DB::table('protocolos');
            // joins
            $protocolos = $protocolos->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
            $protocolos = $protocolos->join('setors', 'setors.id', '=', 'protocolos.setor_id');
            $protocolos = $protocolos->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
            $protocolos = $protocolos->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
            $protocolos = $protocolos->join('users', 'users.id', '=', 'protocolos.user_id');
            $protocolos = $protocolos->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
            // select
            $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador');

            //filtros
            $protocolos = $protocolos->where('protocolos.setor_id', '=', $setor->id); // filtro principal

            if (request()->has('numprotocolo')){
                $protocolos = $protocolos->where('protocolos.id', 'like', '%' . request('numprotocolo') . '%');
            }
            if (request()->has('nome')){
                $protocolos = $protocolos->where('funcionarios.nome', 'like', '%' . request('nome') . '%');
            }
            if (request()->has('setor')){
                $protocolos = $protocolos->where('setors.descricao', 'like', '%' . request('setor') . '%');
            }
            if (request()->has('operador')){
                $protocolos = $protocolos->where('users.name', 'like', '%' . request('operador') . '%');
            }
            if (request()->has('protocolo_tipo_id')){
                if (request('protocolo_tipo_id') != ""){
                    $protocolos = $protocolos->where('protocolos.protocolo_tipo_id', '=', request('protocolo_tipo_id'));
                }
            }
            if (request()->has('protocolo_situacao_id')){
                if (request('protocolo_situacao_id') != ""){
                    $protocolos = $protocolos->where('protocolos.protocolo_situacao_id', '=', request('protocolo_situacao_id'));
                }
            } 
            if (request()->has('protocolo_grupo_id')){
                if (request('protocolo_grupo_id') != ""){
                    $protocolos = $protocolos->where('protocolos.grupo_id', '=', request('protocolo_grupo_id'));
                }
            }
            if (request()->has('dtainicio')){
                 if (request('dtainicio') != ""){
                    $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                    $protocolos = $protocolos->where('protocolos.created_at', '>=', $dataFormatadaMysql);                
                 }
            }
            if (request()->has('dtafinal')){
                 if (request('dtafinal') != ""){
                    // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                    $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                    $protocolos = $protocolos->where('protocolos.created_at', '<=', $dataFormatadaMysql);                
                 }
            }

            $protocolos = $protocolos->orderBy('protocolos.id', 'desc');

            $protocolos = $protocolos->get();

            $this->pdf->SetFont('Arial', '', 10);
            $this->pdf->Cell(20, 6, utf8_decode('Nº'), 1, 0,'R');
            $this->pdf->Cell(20, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(76, 6, utf8_decode('Nome'), 1, 0,'L');
            $this->pdf->Cell(40, 6, utf8_decode('Tipo'), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode('Situação'), 1, 0,'L');
            $this->pdf->Ln();

            foreach ($protocolos as $protocolo) {
                $this->pdf->Cell(20, 6, utf8_decode($protocolo->numero), 1, 0,'R');
                $this->pdf->Cell(20, 6, utf8_decode($protocolo->data), 1, 0,'L');
                $this->pdf->Cell(76, 6, utf8_decode($protocolo->funcionario), 1, 0,'L');
                $this->pdf->Cell(40, 6, utf8_decode($protocolo->tipo_protocolo), 1, 0,'L');
                $this->pdf->Cell(30, 6, utf8_decode($protocolo->situacao_protocolo), 1, 0,'L');
                $this->pdf->Ln();
            }

        }

        $this->pdf->Output('D', 'Protocolos_por_setor_simples_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;       

    }

    /**
     * Exportação para pdf por setor (completo)
     *
     * @param  
     * @return pdf
     */
    public function exportpdfencaminhamento(Request $request)
    {
        if (Gate::denies('protocolo.export')) {
            abort(403, 'Acesso negado.');
        }

      $this->validate($request, [
          'dtaencaminhamento' => 'required|date_format:"d/m/Y"',
          'turnoencaminhamento' => 'required',
        ],
        [
            'dtaencaminhamento.required' => 'Selecione a data para gerar o relatório de encaminhamento',
            'dtaencaminhamento.date_format' => 'Selecione uma data válida para gerar o relatório de encaminhamento',
            'turnoencaminhamento.required' => 'Selecione o turno para gerar o relatório de encaminhamentos',
        ]);

      if (request('turnoencaminhamento') == 1) { //manhã
        $dataInicioFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtaencaminhamento'))->format('Y-m-d 00:00:00');
        $dataFimFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtaencaminhamento'))->format('Y-m-d 11:59:59');
        $turnoTextoImprimir = 'Manhã';
      } else { // tarde
        $dataInicioFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtaencaminhamento'))->format('Y-m-d 12:00:00');
        $dataFimFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtaencaminhamento'))->format('Y-m-d 23:59:59');
        $turnoTextoImprimir = 'Tarde';
      }

      // consulta principal
      $grupos = DB::table('protocolos');
      // joins
      $grupos = $grupos->join('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
      // select
      $grupos = $grupos->select('grupos.id', 'grupos.descricao');

      // filtros
      $grupos = $grupos->where('protocolos.grupo_id', '>', 1); // somente protocolos encaminhados
      
      $grupos = $grupos->where('protocolos.encaminhado_em', '>=', $dataInicioFormatadaMysql); // somente protocolos encaminhados
      
      $grupos = $grupos->where('protocolos.encaminhado_em', '<=', $dataFimFormatadaMysql); // somente protocolos encaminhados

      // group by
      $grupos = $grupos->groupBy('grupos.id', 'grupos.descricao');

      $grupos = $grupos->orderBy('grupos.descricao', 'asc');

      $grupos = $grupos->get();

      // configurações do relatório
      $this->pdf->AliasNbPages();   
      $this->pdf->SetMargins(12, 10, 12);

      foreach ($grupos as $grupo) {
        $this->pdf->AddPage();

        // subtitulo
        $this->pdf->SetFillColor(200);
        $this->pdf->SetTextColor(0);
        $this->pdf->SetDrawColor(0);
        $this->pdf->SetFont('Arial','',14);
        $this->pdf->Cell(186, 8, utf8_decode('Encaminhado para o grupo: ' . $grupo->descricao), 1, 0,'L', 1);
        $this->pdf->Ln();
        $this->pdf->Cell(93, 8, utf8_decode('Data: ' . request('dtaencaminhamento')), 1, 0,'L', 1);
        $this->pdf->Cell(93, 8, utf8_decode('Período da Consulta: ' . $turnoTextoImprimir), 1, 0,'L', 1);
        $this->pdf->Ln();
        $this->pdf->Ln(2);

        // busca e imprime os protocolos para cada grupo
        $protocolos = DB::table('protocolos');
        // joins
        $protocolos = $protocolos->join('funcionarios', 'funcionarios.id', '=', 'protocolos.funcionario_id');
        $protocolos = $protocolos->join('setors', 'setors.id', '=', 'protocolos.setor_id');
        $protocolos = $protocolos->join('protocolo_tipos', 'protocolo_tipos.id', '=', 'protocolos.protocolo_tipo_id');
        $protocolos = $protocolos->join('protocolo_situacaos', 'protocolo_situacaos.id', '=', 'protocolos.protocolo_situacao_id');
        $protocolos = $protocolos->join('users', 'users.id', '=', 'protocolos.user_id');
        $protocolos = $protocolos->leftjoin('grupos', 'grupos.id', '=', 'protocolos.grupo_id');
        $protocolos = $protocolos->join('respostas', 'respostas.id', '=', 'protocolos.resposta_id');
        // select
        $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador',

          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(protocolos.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'protocolos.concluido as concluido',
          'protocolos.grupo_id as grupo_id',
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(protocolos.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'protocolos.concluido_mensagem as resposta_mensagem',

        );

        //filtros
        $protocolos = $protocolos->where('protocolos.grupo_id', '=', $grupo->id); // filtro principal
      
        $protocolos = $protocolos->where('protocolos.encaminhado_em', '>=', $dataInicioFormatadaMysql); // somente protocolos encaminhados
      
        $protocolos = $protocolos->where('protocolos.encaminhado_em', '<=', $dataFimFormatadaMysql); // somente protocolos encaminhados

        $protocolos = $protocolos->orderBy('protocolos.id', 'desc');

        $protocolos = $protocolos->get();

        foreach ($protocolos as $protocolo) {
          $this->pdf->SetFont('Arial', '', 12);
          
          $this->pdf->Cell(40, 6, utf8_decode('Número'), 1, 0,'R');
          $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
          $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
          $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(40, 6, utf8_decode($protocolo->numero), 1, 0,'R');
          $this->pdf->Cell(30, 6, utf8_decode($protocolo->data), 1, 0,'L');
          $this->pdf->Cell(26, 6, utf8_decode($protocolo->hora), 1, 0,'L');
          $this->pdf->Cell(90, 6, utf8_decode($protocolo->operador), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(46, 6, utf8_decode('Matrícula'), 1, 0,'L');
          $this->pdf->Cell(140, 6, utf8_decode('Funcionário'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(46, 6, utf8_decode($protocolo->matricula), 1, 0,'L');
          $this->pdf->Cell(140, 6, utf8_decode($protocolo->funcionario), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(110, 6, utf8_decode('Tipo'), 1, 0,'L');
          $this->pdf->Cell(76, 6, utf8_decode('Situação'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(110, 6, utf8_decode($protocolo->tipo_protocolo), 1, 0,'L');
          $this->pdf->Cell(76, 6, utf8_decode($protocolo->situacao_protocolo), 1, 0,'L');
          $this->pdf->Ln();
          if ($protocolo->observacoes != ''){
              $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->observacoes), 1, 'L', false);
          }
          if ($protocolo->grupo_id > 1){
            $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(126, 6, utf8_decode($protocolo->encaminhado_para), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_encaminhamento), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_encaminhamento), 1, 0,'L');
            $this->pdf->Ln();
          }
          if ($protocolo->concluido == 's'){
            $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(126, 6, utf8_decode($protocolo->resposta), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode($protocolo->data_conclusao), 1, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode($protocolo->hora_conclusao), 1, 0,'L');
            $this->pdf->Ln();
            if ($protocolo->resposta_mensagem != ''){
              $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->MultiCell(186, 6, utf8_decode($protocolo->resposta_mensagem), 1, 'L', false);
            }
          }
          $this->pdf->Ln(2);
        } // end of for each grupo     
      }  

      $this->pdf->Output('D', 'Encaminhamentos_' .  date("Y-m-d H:i:s") . '.pdf', true);
      exit; 

    }            
}
