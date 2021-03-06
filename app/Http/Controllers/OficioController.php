<?php

namespace App\Http\Controllers;

use App\Oficio;
use App\OficioTipo;
use App\OficioSituacao;
use App\OficioTramitacao;
use App\Perpage;
use App\Grupo;
use App\Resposta;

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

class OficioController extends Controller
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
    public function __construct(\App\Reports\OficioReport $pdf)
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

        $oficios = new Oficio;

        // filtros
        if (request()->has('remetente')){
            $oficios = $oficios->where('remetente', 'like', '%' . request('remetente') . '%');
        }

        if (request()->has('numero')){
            $oficios = $oficios->where('id', 'like', '%' . request('numero') . '%');
        }

        if (request()->has('operador')){ // nome do operador que fez o cadastro
            $oficios = $oficios->whereHas('user', function ($query) {
                                                $query->where('name', 'like', '%' . request('operador') . '%');
                                            });
        }

        if (request()->has('oficio_tipo_id')){
            if (request('oficio_tipo_id') != ""){
                $oficios = $oficios->where('oficio_tipo_id', '=', request('oficio_tipo_id'));
            }
        }

        if (request()->has('oficio_situacao_id')){
            if (request('oficio_situacao_id') != ""){
                $oficios = $oficios->where('oficio_situacao_id', '=', request('oficio_situacao_id'));
            }
        }

        if (request()->has('oficio_grupo_id')){
            if (request('oficio_grupo_id') != ""){
                $oficios = $oficios->where('grupo_id', '=', request('oficio_grupo_id'));
            }
        }

        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $oficios = $oficios->where('created_at', '>=', $dataFormatadaMysql);                
             }
        }

        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $oficios = $oficios->where('created_at', '<=', $dataFormatadaMysql);                
             }
        }

        // ordena
        $oficios = $oficios->orderBy('id', 'desc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // consulta a tabela dos tipos de protocolo
        $oficiogrupos = Grupo::orderBy('descricao', 'asc')->get();

        // paginação
        $oficios = $oficios->paginate(session('perPage', '5'))->appends([          
            'remetente' => request('remetente'),
            'numero' => request('numero'),
            'operador' => request('operador'),
            'oficio_tipo_id' => request('oficio_tipo_id'),
            'oficio_situacao_id' => request('oficio_situacao_id'),
            'dtainicio' => request('dtainicio'),
            'dtafinal' => request('dtafinal'),          
            ]);

        // tabelas auxiliares usadas pelo filtro
        $oficiotipos = OficioTipo::orderBy('descricao', 'asc')->get();

        $oficiosituacoes = OficioSituacao::orderBy('descricao', 'asc')->get();

        return view('oficios.index', compact('oficios', 'perpages', 'oficiotipos', 'oficiosituacoes', 'oficiogrupos'));
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

        $oficiotipos = OficioTipo::orderBy('descricao', 'asc')->get();

        return view('oficios.create', compact('oficiotipos'));
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

        $oficio_input = $request->all();

        // gera uma chave aleatória de 20 caracteres
        $oficio_input['chave'] = generateRandomString(20);

        // grupo de trabalho padrão
        $oficio_input['grupo_id'] = 1; // não encaminhado para nenhuma grupo

        // dados da conclusão
        $oficio_input['concluido'] = 'n'; // ainda não foi concluido
        $oficio_input['resposta_id'] = 1; // ainda não disponível

        // situação do ofício
        $oficio_input['oficio_situacao_id'] = 1;

        // recebi o usuário logado no sistema
        $user = Auth::user();

        $oficio_input['user_id'] = $user->id;

        $this->validate($request, [
          'remetente' => 'required',
          'oficio_tipo_id' => 'required',
        ],
        [
            'remetente.required' => 'Preencha o campo de remetente(s)',
            'oficio_tipo_id.required' => 'Selecione o tipo de ofício',
        ]);

        // salvar o barcode
        $urlImageFile = public_path() . '\qrcodes\\' . $oficio_input['chave'] . '.png';
        $urlLinkPublic = $request->url() . '/' . $oficio_input['chave'] . '/buscar';

        // salva a imagem com o barcode
        QrCode::format('png')->size(250)->margin(1)->generate($urlLinkPublic, $urlImageFile);

        $oficio = Oficio::create($oficio_input); //salva

        #mudar aqui

        Session::flash('create_oficio', 'Ofício Nº ' . $oficio->id . ' cadastrado com sucesso!');

        return Redirect::route('oficios.edit', $oficio->id);
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

        $oficio = Oficio::findOrFail($id);

        $tramitacoes = OficioTramitacao::where('oficio_id', '=', $id)->orderBy('id', 'desc')->get();

        return view('oficios.show', compact('oficio', 'tramitacoes'));
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

        $oficio = Oficio::findOrFail($id);

        $oficiotramitacoes = OficioTramitacao::where('oficio_id', '=', $id)->orderBy('id', 'desc')->get();

        $anexos = $oficio->anexos()->orderBy('id', 'desc')->get();

        $oficiotipos = OficioTipo::orderBy('descricao', 'asc')->get();

        $oficiosituacoes = OficioSituacao::orderBy('descricao', 'asc')->get();

        $grupos = Grupo::orderBy('descricao', 'asc')->get();      
        
        $respostas = Resposta::orderBy('descricao', 'asc')->get();

        return view('oficios.edit', compact('oficio', 'oficiotipos', 'oficiosituacoes', 'oficiotramitacoes', 'anexos', 'grupos', 'respostas'));
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
            'oficio_tipo_id' => 'required',
        ],
        [
            'remetente.required' => 'Preencha o campo de remetente(s)',
            'oficio_tipo_id.required' => 'Selecione o tipo de ofício',
        ]);

        $oficio = Oficio::findOrFail($id);
            
        $oficio->update($request->all());
        
        Session::flash('edited_oficio', 'Ofício n° ' . $oficio->id . ' alterado com sucesso!');

        return redirect(route('oficios.edit', $id));
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

        Oficio::findOrFail($id)->delete();

        Session::flash('deleted_oficio', 'Ofício excluído com sucesso!');

        return redirect(route('oficios.index'));
    }

    /**
     * Preenche a vaga com o funcionario selecionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function concluir(Request $request, $id)
    {
      if (Gate::denies('oficio.concluir')) {
            abort(403, 'Acesso negado.');
      }

      $this->validate($request, [
          'resposta_id' => 'required',
        ],
        [
            'resposta_id.required' => 'Selecione a resposta da conclusão do ofício',
        ]);

      $oficio_input = $request->all();

      $oficio = Oficio::findOrFail($id);

      $oficio->concluido_mensagem = $oficio_input['concluido_mensagem'];

      $oficio->concluido = 's';

      $oficio->concluido_em = Carbon::now()->toDateTimeString();

      $oficio->resposta_id = $oficio_input['resposta_id'];

      $oficio->oficio_situacao_id = 4; // concluido

      $oficio->save();

      return redirect(route('oficios.edit', $id));
    }


    /**
     * Preenche a vaga com o funcionario selecionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function encaminhar(Request $request, $id)
    {
      if (Gate::denies('oficio.encaminhar')) {
            abort(403, 'Acesso negado.');
      }

      $this->validate($request, [
          'grupo_id' => 'required',
        ],
        [
            'grupo_id.required' => 'Selecione o grupo a ser encaminhado o ofício',
        ]);

      $óficio_input = $request->all();

      $oficio = Oficio::findOrFail($id);

      $oficio->grupo_id = $óficio_input['grupo_id'];

      $oficio->oficio_situacao_id = 2; // encaminhado

      $oficio->encaminhado_em = Carbon::now()->toDateTimeString();

      $oficio->save();

      return redirect(route('oficios.edit', $id));
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
            ,   'Content-Disposition' => 'attachment; filename=Ofícios_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $oficios = DB::table('oficios');
        // joins
        $oficios = $oficios->join('oficio_tipos', 'oficio_tipos.id', '=', 'oficios.oficio_tipo_id');
        $oficios = $oficios->join('oficio_situacaos', 'oficio_situacaos.id', '=', 'oficios.oficio_situacao_id');
        $oficios = $oficios->join('users', 'users.id', '=', 'oficios.user_id');
        $oficios = $oficios->leftjoin('grupos', 'grupos.id', '=', 'oficios.grupo_id');
        $oficios = $oficios->join('respostas', 'respostas.id', '=', 'oficios.resposta_id');
        // select
        $oficios = $oficios->select('oficios.id as numeroRH', DB::raw('DATE_FORMAT(oficios.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(oficios.created_at, \'%H:%i\') AS hora'),'oficios.remetente', 'oficio_tipos.descricao as tipo_oficio', 'oficio_situacaos.descricao as situacao_oficio', 'oficios.observacao', 'users.name as operador', 
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'oficios.concluido as concluido',
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'oficios.concluido_mensagem as resposta_mensagem',
        );
        // filtros
        if (request()->has('numeromemorando')){
            $oficios = $oficios->where('oficios.id', 'like', '%' . request('numeromemorando') . '%');
        }
        if (request()->has('remetente')){
            $oficios = $oficios->where('oficios.remetente', 'like', '%' . request('remetente') . '%');
        }
        if (request()->has('operador')){
            $oficios = $oficios->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('oficio_tipo_id')){
            if (request('oficio_tipo_id') != ""){
                $oficios = $oficios->where('oficios.oficio_tipo_id', '=', request('oficio_tipo_id'));
            }
        }
        if (request()->has('oficio_situacao_id')){
            if (request('oficio_situacao_id') != ""){
                $oficios = $oficios->where('oficios.oficio_situacao_id', '=', request('oficio_situacao_id'));
            }
        }
        if (request()->has('oficio_grupo_id')){
            if (request('oficio_grupo_id') != ""){
                $oficios = $oficios->where('oficios.grupo_id', '=', request('oficio_grupo_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $oficios = $oficios->where('oficios.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $oficios = $oficios->where('oficios.created_at', '<=', $dataFormatadaMysql);                
             }
        }
        $oficios = $oficios->orderBy('oficios.id', 'desc');

        $list = $oficios->get()->toArray();

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
        if (Gate::denies('oficio.export')) {
            abort(403, 'Acesso negado.');
        }

        $oficios = DB::table('oficios');
        // joins
        $oficios = $oficios->join('oficio_tipos', 'oficio_tipos.id', '=', 'oficios.oficio_tipo_id');
        $oficios = $oficios->join('oficio_situacaos', 'oficio_situacaos.id', '=', 'oficios.oficio_situacao_id');
        $oficios = $oficios->join('users', 'users.id', '=', 'oficios.user_id');
        $oficios = $oficios->leftjoin('grupos', 'grupos.id', '=', 'oficios.grupo_id');
        $oficios = $oficios->join('respostas', 'respostas.id', '=', 'oficios.resposta_id');
        // select
        $oficios = $oficios->select('oficios.id as numeroRH', DB::raw('DATE_FORMAT(oficios.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(oficios.created_at, \'%H:%i\') AS hora'),'oficios.remetente', 'oficio_tipos.descricao as tipo_oficio', 'oficio_situacaos.descricao as situacao_oficio', 'oficios.observacao', 'users.name as operador',
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'oficios.concluido as concluido',
          'oficios.grupo_id as grupo_id',
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'oficios.concluido_mensagem as resposta_mensagem',
        );
        // filtros
        if (request()->has('numeromemorando')){
            $oficios = $oficios->where('oficios.id', 'like', '%' . request('numeromemorando') . '%');
        }
        if (request()->has('remetente')){
            $oficios = $oficios->where('oficios.remetente', 'like', '%' . request('remetente') . '%');
        }
        if (request()->has('operador')){
            $oficios = $oficios->where('users.name', 'like', '%' . request('operador') . '%');
        }
        if (request()->has('oficio_tipo_id')){
            if (request('oficio_tipo_id') != ""){
                $oficios = $oficios->where('oficios.oficio_tipo_id', '=', request('oficio_tipo_id'));
            }
        }
        if (request()->has('oficio_situacao_id')){
            if (request('oficio_situacao_id') != ""){
                $oficios = $oficios->where('oficios.oficio_situacao_id', '=', request('oficio_situacao_id'));
            }
        }
        if (request()->has('oficio_grupo_id')){
            if (request('oficio_grupo_id') != ""){
                $oficios = $oficios->where('oficios.grupo_id', '=', request('oficio_grupo_id'));
            }
        } 
        if (request()->has('dtainicio')){
             if (request('dtainicio') != ""){
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d 00:00:00');           
                $oficios = $oficios->where('oficios.created_at', '>=', $dataFormatadaMysql);                
             }
        }
        if (request()->has('dtafinal')){
             if (request('dtafinal') != ""){
                // converte o formato de entrada dd/mm/yyyy para o formato aceito pelo mysql
                $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d 23:59:59');         
                $oficios = $oficios->where('oficios.created_at', '<=', $dataFormatadaMysql);                
             }
        }
        $oficios = $oficios->orderBy('oficios.id', 'desc');

        $oficios = $oficios->get();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        foreach ($oficios as $oficio) {
            $this->pdf->Cell(40, 6, utf8_decode('Nº'), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(40, 6, utf8_decode($oficio->numeroRH), 1, 0,'R');
            $this->pdf->Cell(30, 6, utf8_decode($oficio->data), 1, 0,'L');
            $this->pdf->Cell(26, 6, utf8_decode($oficio->hora), 1, 0,'L');
            $this->pdf->Cell(90, 6, utf8_decode($oficio->operador), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(186, 6, utf8_decode('Remetente(s)/Assunto'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($oficio->remetente), 1, 'L', false);
            $this->pdf->Cell(93, 6, utf8_decode('Tipo'), 1, 0,'L');
            $this->pdf->Cell(93, 6, utf8_decode('Situacao'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->Cell(93, 6, utf8_decode($oficio->tipo_oficio), 1, 0,'L');
            $this->pdf->Cell(93, 6, utf8_decode($oficio->situacao_oficio), 1, 0,'L');
            $this->pdf->Ln();
            if ($oficio->observacao != ''){
                $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->MultiCell(186, 6, utf8_decode($oficio->observacao), 1, 'L', false);
            }
            if ($oficio->grupo_id > 1){
              $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->Cell(126, 6, utf8_decode($oficio->encaminhado_para), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($oficio->data_encaminhamento), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($oficio->hora_encaminhamento), 1, 0,'L');
              $this->pdf->Ln();
            }
            if ($oficio->concluido == 's'){
              $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
              $this->pdf->Ln();
              $this->pdf->Cell(126, 6, utf8_decode($oficio->resposta), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($oficio->data_conclusao), 1, 0,'L');
              $this->pdf->Cell(30, 6, utf8_decode($oficio->hora_conclusao), 1, 0,'L');
              $this->pdf->Ln();
              if ($oficio->resposta_mensagem != ''){
                $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
                $this->pdf->Ln();
                $this->pdf->MultiCell(186, 6, utf8_decode($oficio->resposta_mensagem), 1, 'L', false);
              }
            }

            // tramitações
            // consulta secundariatramitacoes
            $tramitacoes = DB::table('oficio_tramitacaos');
            // joins
            $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'oficio_tramitacaos.funcionario_id');
            $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'oficio_tramitacaos.setor_id');
            $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'oficio_tramitacaos.user_id');
            // select
            $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(oficio_tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(oficio_tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'oficio_tramitacaos.descricao as observacoes');
            // filter
            $tramitacoes = $tramitacoes->where('oficio_tramitacaos.oficio_id', '=', $oficio->numeroRH);
            // ordena
            $tramitacoes = $tramitacoes->orderBy('oficio_tramitacaos.id', 'desc');
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

        $this->pdf->Output('D', 'Ofícios_' .  date("Y-m-d H:i:s") . '.pdf', true);
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
        if (Gate::denies('oficio.export')) {
            abort(403, 'Acesso negado.');
        }

        $oficio = DB::table('oficios');
        // joins
        $oficio = $oficio->join('oficio_tipos', 'oficio_tipos.id', '=', 'oficios.oficio_tipo_id');
        $oficio = $oficio->join('oficio_situacaos', 'oficio_situacaos.id', '=', 'oficios.oficio_situacao_id');
        $oficio = $oficio->join('users', 'users.id', '=', 'oficios.user_id');
        $oficio = $oficio->leftjoin('grupos', 'grupos.id', '=', 'oficios.grupo_id');
        $oficio = $oficio->join('respostas', 'respostas.id', '=', 'oficios.resposta_id');
        // select
        $oficio = $oficio->select('oficios.id as numeroRH', DB::raw('DATE_FORMAT(oficios.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(oficios.created_at, \'%H:%i\') AS hora'),'oficios.remetente', 'oficio_tipos.descricao as tipo_oficio', 'oficio_situacaos.descricao as situacao_oficio', 'oficios.observacao', 'users.name as operador', 'oficios.chave',
          DB::raw("coalesce(grupos.descricao, '-') as encaminhado_para"), 
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%d/%m/%Y\') AS data_encaminhamento'),
          DB::raw('DATE_FORMAT(oficios.encaminhado_em, \'%H:%i\') AS hora_encaminhamento'),
          'oficios.concluido as concluido',
          'oficios.grupo_id as grupo_id',
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%d/%m/%Y\') AS data_conclusao'),
          DB::raw('DATE_FORMAT(oficios.concluido_em, \'%H:%i\') AS hora_conclusao'),
          DB::raw("coalesce(respostas.descricao, '-') as resposta"),
          'oficios.concluido_mensagem as resposta_mensagem',
        );
        // filtros
        //filtros
        $oficio = $oficio->where('oficios.id', '=', $id);
        // get
        $oficio = $oficio->get()->first();

        // configurações do relatório
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();
        $this->pdf->Cell(40, 6, utf8_decode('Nº'), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode('Hora'), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode('Operador'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(40, 6, utf8_decode($oficio->numeroRH), 1, 0,'R');
        $this->pdf->Cell(30, 6, utf8_decode($oficio->data), 1, 0,'L');
        $this->pdf->Cell(26, 6, utf8_decode($oficio->hora), 1, 0,'L');
        $this->pdf->Cell(90, 6, utf8_decode($oficio->operador), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(186, 6, utf8_decode('Remetente(s)/Assunto'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->MultiCell(186, 6, utf8_decode($oficio->remetente), 1, 'L', false);
        $this->pdf->Cell(93, 6, utf8_decode('Tipo'), 1, 0,'L');
        $this->pdf->Cell(93, 6, utf8_decode('Situacao'), 1, 0,'L');
        $this->pdf->Ln();
        $this->pdf->Cell(93, 6, utf8_decode($oficio->tipo_oficio), 1, 0,'L');
        $this->pdf->Cell(93, 6, utf8_decode($oficio->situacao_oficio), 1, 0,'L');
        $this->pdf->Ln();
        if ($oficio->observacao != ''){
            $this->pdf->Cell(186, 6, utf8_decode('Observações'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($oficio->observacao), 1, 'L', false);
        }
        if ($oficio->grupo_id > 1){
          $this->pdf->Cell(126, 6, utf8_decode('Encaminhado para'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(126, 6, utf8_decode($oficio->encaminhado_para), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($oficio->data_encaminhamento), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($oficio->hora_encaminhamento), 1, 0,'L');
          $this->pdf->Ln();
        }
        if ($oficio->concluido == 's'){
          $this->pdf->Cell(126, 6, utf8_decode('Resposta da conclusão'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Data'), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode('Hora'), 1, 0,'L');
          $this->pdf->Ln();
          $this->pdf->Cell(126, 6, utf8_decode($oficio->resposta), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($oficio->data_conclusao), 1, 0,'L');
          $this->pdf->Cell(30, 6, utf8_decode($oficio->hora_conclusao), 1, 0,'L');
          $this->pdf->Ln();
          if ($oficio->resposta_mensagem != ''){
            $this->pdf->Cell(186, 6, utf8_decode('Mensagem de resposta'), 1, 0,'L');
            $this->pdf->Ln();
            $this->pdf->MultiCell(186, 6, utf8_decode($oficio->resposta_mensagem), 1, 'L', false);
          }
        }
        // tramitações
        // consulta secundariatramitacoes
        $tramitacoes = DB::table('oficio_tramitacaos');
        // joins
        $tramitacoes = $tramitacoes->leftJoin('funcionarios', 'funcionarios.id', '=', 'oficio_tramitacaos.funcionario_id');
        $tramitacoes = $tramitacoes->leftJoin('setors', 'setors.id', '=', 'oficio_tramitacaos.setor_id');
        $tramitacoes = $tramitacoes->join('users', 'users.id', '=', 'oficio_tramitacaos.user_id');
        // select
        $tramitacoes = $tramitacoes->select(DB::raw('DATE_FORMAT(oficio_tramitacaos.created_at, \'%d/%m/%Y\') AS data'), DB::raw('DATE_FORMAT(oficio_tramitacaos.created_at, \'%H:%i\') AS hora'), 'funcionarios.nome as funcionario', 'funcionarios.matricula as matricula', 'setors.descricao as setor', 'setors.codigo as codigo', 'users.name as operador', 'oficio_tramitacaos.descricao as observacoes');
        // filter
        $tramitacoes = $tramitacoes->where('oficio_tramitacaos.oficio_id', '=', $oficio->numeroRH);
        // ordena
        $tramitacoes = $tramitacoes->orderBy('oficio_tramitacaos.id', 'desc');
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
        $urlLinkPublic = 'qrcodes/' . $oficio->chave . '.png';

        $this->pdf->Image($urlLinkPublic, null, null, 0, 0, 'PNG');

        $this->pdf->Ln(2);

        $this->pdf->Output('D', 'Ofício_num' . $id . '_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }          
}
