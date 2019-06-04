<?php

namespace App\Http\Controllers;

use App\Memorando;
use App\MemorandoTipo;
use App\MemorandoSituacao;
use App\MemorandoTramitacao;
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
        if (Gate::denies('memorando.index')) {
            abort(403, 'Acesso negado.');
        }

        $memorandos = new Memorando;

        // filtros
        if (request()->has('remetente')){
            $memorandos = $memorandos->where('remetente', 'like', '%' . request('remetente') . '%');
        }

        if (request()->has('numeromemorando')){
            $memorandos = $memorandos->where('id', 'like', '%' . request('numeromemorando') . '%');
        }

        if (request()->has('memorando_tipo_id')){
            if (request('memorando_tipo_id') != ""){
                $memorandos = $memorandos->where('memorando_tipo_id', '=', request('memorando_tipo_id'));
            }
        }

        if (request()->has('memorando_situacao_id')){
            if (request('memorando_situacao_id') != ""){
                $memorandos = $memorandos->where('memorando_situacao_id', '=', request('memorando_situacao_id'));
            }
        }

        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $memorandos = $memorandos->where('created_at', '>=', $dataFormatadaMysql);                
             }
        }

        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $memorandos = $memorandos->where('created_at', '<=', $dataFormatadaMysql);                
             }
        }

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
        $memorandos = $memorandos->paginate(session('perPage', '5'))->appends([          
            'remetente' => request('remetente'),
            'numeromemorando' => request('numeromemorando'),
            'memorando_tipo_id' => request('memorando_tipo_id'),
            'memorando_situacao_id' => request('memorando_situacao_id'),
            'dtainicio' => request('dtainicio'),
            'dtafinal' => request('dtafinal'),          
            ]);

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
        if (Gate::denies('memorando.create')) {
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
        $urlLinkPublic = $request->url() . '/' . $memorando_input['chave'] . '/buscar';

        // salva a imagem com o barcode
        QrCode::format('png')->size(250)->margin(1)->generate($urlLinkPublic, $urlImageFile);

        $memorando = Memorando::create($memorando_input); //salva

        #mudar aqui

        Session::flash('create_memorando', 'Memorando Nº ' . $memorando->id . ' cadastrado com sucesso!');

        //return redirect(route('memorandos.index'));

        return Redirect::route('memorandos.edit', $memorando->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('memorando.show')) {
            abort(403, 'Acesso negado.');
        }

        $memorando = Memorando::findOrFail($id);

        return view('memorandos.show', compact('memorando'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('memorando.edit')) {
            abort(403, 'Acesso negado.');
        }

        $memorando = Memorando::findOrFail($id);

        $memorandotramitacoes = MemorandoTramitacao::where('memorando_id', '=', $id)->orderBy('id', 'desc')->get();

        $memorandotipos = MemorandoTipo::orderBy('descricao', 'asc')->get();

        $memorandosituacoes = MemorandoSituacao::orderBy('descricao', 'asc')->get();

        return view('memorandos.edit', compact('memorando', 'memorandotipos', 'memorandotipos', 'memorandosituacoes', 'memorandotramitacoes'));
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

        $memorando = Memorando::findOrFail($id);
            
        $memorando->update($request->all());
        
        Session::flash('edited_memorando', 'Memorando n° ' . $memorando->id . ' alterado com sucesso!');

        return redirect(route('memorandos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('memorando.delete')) {
            abort(403, 'Acesso negado.');
        }

        Memorando::findOrFail($id)->delete();

        Session::flash('deleted_memorando', 'Memorando excluído com sucesso!');

        return redirect(route('memorandos.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('memorando.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Memorandos_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $memorandos = DB::table('memorandos');
        // joins
        $memorandos = $memorandos->join('memorando_tipos', 'memorando_tipos.id', '=', 'memorandos.memorando_tipo_id');
        $memorandos = $memorandos->join('memorando_situacaos', 'memorando_situacaos.id', '=', 'memorandos.memorando_situacao_id');
        $memorandos = $memorandos->join('users', 'users.id', '=', 'memorandos.user_id');
        // select
        $memorandos = $memorandos->select('memorandos.id as numeroRH', DB::raw('DATE_FORMAT(memorandos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(memorandos.created_at, \'%H:%i\') AS hora'),'memorandos.remetente', 'memorando_tipos.descricao as tipo_memorando', 'memorando_situacaos.descricao as situacao_memorando', 'memorandos.observacao', 'users.name as operador');
        // filtros
        if (request()->has('numeromemorando')){
            $memorandos = $memorandos->where('memorandos.id', 'like', '%' . request('numeromemorando') . '%');
        }
        if (request()->has('remetente')){
            $memorandos = $memorandos->where('memorandos.remetente', 'like', '%' . request('remetente') . '%');
        }
        if (request()->has('memorando_tipo_id')){
            if (request('memorando_tipo_id') != ""){
                $memorandos = $memorandos->where('memorandos.memorando_tipo_id', '=', request('memorando_tipo_id'));
            }
        }
        if (request()->has('memorando_situacao_id')){
            if (request('memorando_situacao_id') != ""){
                $memorandos = $memorandos->where('memorandos.memorando_situacao_id', '=', request('memorando_situacao_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $memorandos = $memorandos->where('memorandos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $memorandos = $memorandos->where('memorandos.created_at', '<=', $dataFormatadaMysql);                
             }
        }
        $memorandos = $memorandos->orderBy('memorandos.id', 'desc');

        $list = $memorandos->get()->toArray();

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
        if (Gate::denies('memorando.export')) {
            abort(403, 'Acesso negado.');
        }

        $memorandos = DB::table('memorandos');
        // joins
        $memorandos = $memorandos->join('memorando_tipos', 'memorando_tipos.id', '=', 'memorandos.memorando_tipo_id');
        $memorandos = $memorandos->join('memorando_situacaos', 'memorando_situacaos.id', '=', 'memorandos.memorando_situacao_id');
        $memorandos = $memorandos->join('users', 'users.id', '=', 'memorandos.user_id');
        // select
        $memorandos = $memorandos->select('memorandos.id as numeroRH', DB::raw('DATE_FORMAT(memorandos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(memorandos.created_at, \'%H:%i\') AS hora'),'memorandos.remetente', 'memorando_tipos.descricao as tipo_memorando', 'memorando_situacaos.descricao as situacao_memorando', 'memorandos.observacao', 'users.name as operador');
        // filtros
        if (request()->has('numeromemorando')){
            $memorandos = $memorandos->where('memorandos.id', 'like', '%' . request('numeromemorando') . '%');
        }
        if (request()->has('remetente')){
            $memorandos = $memorandos->where('memorandos.remetente', 'like', '%' . request('remetente') . '%');
        }
        if (request()->has('memorando_tipo_id')){
            if (request('memorando_tipo_id') != ""){
                $memorandos = $memorandos->where('memorandos.memorando_tipo_id', '=', request('memorando_tipo_id'));
            }
        }
        if (request()->has('memorando_situacao_id')){
            if (request('memorando_situacao_id') != ""){
                $memorandos = $memorandos->where('memorandos.memorando_situacao_id', '=', request('memorando_situacao_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $memorandos = $memorandos->where('memorandos.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $memorandos = $memorandos->where('memorandos.created_at', '<=', $dataFormatadaMysql);                
             }
        }
        $memorandos = $memorandos->orderBy('memorandos.id', 'desc');

        $memorandos = $memorandos->get();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        foreach ($memorandos as $memorando) {
            $this->pdf->Cell(40, 6, utf8_decode('Nº(RH)'), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(40, 6, utf8_decode($memorando->numeroRH), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode($memorando->data), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode($memorando->hora), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode($memorando->operador), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(186, 6, utf8_decode('Remetente'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($memorando->remetente), 1, 'L', false);
            $this->pdf->Cell(93, 6, utf8_decode('Tipo'), 1, 0,'L');
            $this->pdf->Cell(93, 6, utf8_decode('Situacao'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(93, 6, utf8_decode($memorando->tipo_memorando), 1, 0,'L');
            $this->pdf->Cell(93, 6, utf8_decode($memorando->situacao_memorando), 1, 0,'L');
            $this->pdf->Ln();
            if ($memorando->observacao != ''){
                $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->MultiCell(186, 6, utf8_decode($memorando->observacao), 1, 'L', false);
            }

            // tramitações
            // consulta secundariatramitacoes
            $tramitacoes = DB::table('memorando_tramitacaos');
            // joins
            $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'memorando_tramitacaos.funcionario_id');
            $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'memorando_tramitacaos.setor_id');
            $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'memorando_tramitacaos.user_id');
            // select
            $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(memorando_tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(memorando_tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'memorando_tramitacaos.descricao as observacoes');
            // filter
            $tramitacoes = $tramitacoes->where('memorando_tramitacaos.memorando_id', '=', $memorando->numeroRH);
            // ordena
            $tramitacoes = $tramitacoes->orderBy('memorando_tramitacaos.id', 'desc');
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

        $this->pdf->Output('D', 'Memorandos_' .  date("Y-m-d H:i:s") . '.pdf', true);
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
        if (Gate::denies('memorando.export')) {
            abort(403, 'Acesso negado.');
        }

        $memorando = DB::table('memorandos');
        // joins
        $memorando = $memorando->join('memorando_tipos', 'memorando_tipos.id', '=', 'memorandos.memorando_tipo_id');
        $memorando = $memorando->join('memorando_situacaos', 'memorando_situacaos.id', '=', 'memorandos.memorando_situacao_id');
        $memorando = $memorando->join('users', 'users.id', '=', 'memorandos.user_id');
        // select
        $memorando = $memorando->select('memorandos.id as numeroRH', DB::raw('DATE_FORMAT(memorandos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(memorandos.created_at, \'%H:%i\') AS hora'),'memorandos.remetente', 'memorando_tipos.descricao as tipo_memorando', 'memorando_situacaos.descricao as situacao_memorando', 'memorandos.observacao', 'users.name as operador', 'memorandos.chave');
        // filtros
        //filtros
        $memorando = $memorando->where('memorandos.id', '=', $id);
        // get
        $memorando = $memorando->get()->first();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();
        $this->pdf->Cell(40, 6, utf8_decode('Nº(RH)'), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(40, 6, utf8_decode($memorando->numeroRH), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode($memorando->data), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode($memorando->hora), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode($memorando->operador), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(186, 6, utf8_decode('Remetente'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->MultiCell(186, 6, utf8_decode($memorando->remetente), 1, 'L', false);
        $this->pdf->Cell(93, 6, utf8_decode('Tipo'), 1, 0,'L');
        $this->pdf->Cell(93, 6, utf8_decode('Situacao'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(93, 6, utf8_decode($memorando->tipo_memorando), 1, 0,'L');
        $this->pdf->Cell(93, 6, utf8_decode($memorando->situacao_memorando), 1, 0,'L');
        $this->pdf->Ln();
        if ($memorando->observacao != ''){
            $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($memorando->observacao), 1, 'L', false);
        }
        // tramitações
        // consulta secundariatramitacoes
        $tramitacoes = DB::table('memorando_tramitacaos');
        // joins
        $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'memorando_tramitacaos.funcionario_id');
        $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'memorando_tramitacaos.setor_id');
        $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'memorando_tramitacaos.user_id');
        // select
        $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(memorando_tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(memorando_tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'memorando_tramitacaos.descricao as observacoes');
        // filter
        $tramitacoes = $tramitacoes->where('memorando_tramitacaos.memorando_id', '=', $memorando->numeroRH);
        // ordena
        $tramitacoes = $tramitacoes->orderBy('memorando_tramitacaos.id', 'desc');
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
        $urlLinkPublic = 'qrcodes/' . $memorando->chave . '.png';

        $this->pdf->Image($urlLinkPublic, null, null, 0, 0, 'PNG');

        $this->pdf->Ln(2);

        $this->pdf->Output('D', 'Memorando_num' . $id . '_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }                   
}
