<?php

namespace App\Http\Controllers;

use App\SolicitacaoSituacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class SolicitacaoSituacaoController extends Controller
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
    public function __construct(\App\Reports\SolicitacaoSituacaoReport $pdf)
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
        if (Gate::denies('solicitacaosituacao.index')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaosituacoes = new SolicitacaoSituacao;

        // ordena
        $solicitacaosituacoes = $solicitacaosituacoes->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $solicitacaosituacoes = $solicitacaosituacoes->paginate(session('perPage', '5'));

        return view('solicitacaosituacoes.index', compact('solicitacaosituacoes', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('solicitacaosituacao.create')) {
            abort(403, 'Acesso negado.');
        }   
        return view('solicitacaosituacoes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'descricao' => 'required',
        ]);

        $solicitacaoSituacao = $request->all();

        SolicitacaoSituacao::create($solicitacaoSituacao); //salva

        Session::flash('create_solicitacaosituacao', 'Situação da solicitação cadastrado com sucesso!');

        return redirect(route('solicitacaosituacoes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('solicitacaosituacao.show')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaosituacoes = SolicitacaoSituacao::findOrFail($id);

        return view('solicitacaosituacoes.show', compact('solicitacaosituacoes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('solicitacaosituacao.edit')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaosituacao = SolicitacaoSituacao::findOrFail($id);

        return view('solicitacaosituacoes.edit', compact('solicitacaosituacao'));
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
          'descricao' => 'required',
        ]);

        $solicitacaosituacao = SolicitacaoSituacao::findOrFail($id);
            
        $solicitacaosituacao->update($request->all());
        
        Session::flash('edited_solicitacaosituacao', 'Situação da solicitação alterada com sucesso!');

        return redirect(route('solicitacaosituacoes.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('solicitacaosituacao.delete')) {
            abort(403, 'Acesso negado.');
        }

        SolicitacaoSituacao::findOrFail($id)->delete();

        Session::flash('deleted_solicitacaosituacao', 'Situação da solicitação excluída com sucesso!');

        return redirect(route('solicitacaosituacoes.index'));   
    }


    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('solicitacaosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=SituacoesSolicitacoes_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $solicitacaosituacoes = DB::table('solicitacao_situacaos');

        $solicitacaosituacoes = $solicitacaosituacoes->select('descricao');

        $solicitacaosituacoes = $solicitacaosituacoes->orderBy('descricao', 'asc');

        $list = $solicitacaosituacoes->get()->toArray();

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
        if (Gate::denies('solicitacaosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $solicitacaosituacoes = DB::table('solicitacao_situacaos');

        $solicitacaosituacoes = $solicitacaosituacoes->select('descricao');

        $solicitacaosituacoes = $solicitacaosituacoes->orderBy('descricao', 'asc');    

        $solicitacaosituacoes = $solicitacaosituacoes->get();

        foreach ($solicitacaosituacoes as $solicitacaosituacao) {
            $this->pdf->Cell(186, 6, utf8_decode($solicitacaosituacao->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'OficioSituacao_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }       
}
