<?php

namespace App\Http\Controllers;

use App\Protocolo;
use App\Tramitacao;
use App\Periodo;
use App\ProtocoloSituacao;
use App\ProtocoloTipo;
use App\PeriodoTipo;
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

        // paginação
        $protocolos = $protocolos->paginate(session('perPage', '5'))->appends([          
            'numprotocolo' => request('numprotocolo'),
            'nome' => request('nome'),
            'setor' => request('setor'),
            'protocolo_tipo_id' => request('protocolo_tipo_id'),
            'protocolo_situacao_id' => request('protocolo_situacao_id'),
            'dtainicio' => request('dtainicio'),
            'dtafinal' => request('dtafinal'),          
            ]);

        return view('protocolos.index', compact('protocolos', 'perpages', 'protocolosituacoes', 'protocolotipos'));
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

        // geração de uma string aleatória de tamanho configurável
        function generateRandomString($length = 10) {
            return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
        }

        $protocolo_input = $request->all();

        // gera uma chave aleatória de 20 caracteres
        $protocolo_input['chave'] = generateRandomString(20);

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $protocolo_input['user_id'] = $user->id;

        $this->validate($request, [
          'funcionario_id' => 'required',
          'setor_id' => 'required',
          'protocolo_tipo_id' => 'required',
          'protocolo_situacao_id' => 'required',
        ],
        [
            'funcionario_id.required' => 'Selecione um funcionário para o protocolo',
            'setor_id.required' => 'Selecione o setor para esse protocolo',
            'protocolo_tipo_id.required' => 'Selecione o tipo do protocolo',
            'protocolo_situacao_id.required' => 'Selecione a situação do protocolo',
        ]);

        // salvar o barcode
        $urlImageFile = public_path() . '\qrcodes\\' . $protocolo_input['chave'] . '.png';
        $urlLinkPublic = $request->url() . '/protocolos/' . $protocolo_input['chave'] . '/buscar';

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

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get(); 

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();        

        return view('protocolos.edit', compact('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos', 'tramitacoes'));

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
        if (Gate::denies('protocolo.delete')) {
            abort(403, 'Acesso negado.');
        }

        Protocolo::findOrFail($id)->delete();

        Session::flash('deleted_protocolo', 'Protocolo excluído com sucesso!');

        return redirect(route('protocolos.index'));
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
        // select
        $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador');

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
        // select
        $protocolos = $protocolos->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador');

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
        // select
        $protocolo = $protocolo->select('protocolos.id as numero', DB::raw('DATE_FORMAT(protocolos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(protocolos.created_at, \'%H:%i\') AS hora'),'protocolos.descricao as observacoes', 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo_setor', 'protocolo_tipos.descricao as tipo_protocolo', 'protocolo_situacaos.descricao as situacao_protocolo', 'users.name as operador', 'protocolos.chave');

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

        $this->pdf->Output('D', 'Protocolos_num' . $id . '_' .  date("Y-m-d H:i:s") . '.pdf', true);
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

            //

            $this->pdf->SetFont('Arial', '', 12);

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
}
