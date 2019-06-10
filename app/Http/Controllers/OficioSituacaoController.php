<?php

namespace App\Http\Controllers;

use App\OficioSituacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class OficioSituacaoController extends Controller
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
    public function __construct(\App\Reports\OficioSituacaoReport $pdf)
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
        if (Gate::denies('oficiosituacao.index')) {
            abort(403, 'Acesso negado.');
        }

        $oficiosituacoes = new OficioSituacao;

        // ordena
        $oficiosituacoes = $oficiosituacoes->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $oficiosituacoes = $oficiosituacoes->paginate(session('perPage', '5'));

        return view('oficiosituacoes.index', compact('oficiosituacoes', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('oficiosituacao.create')) {
            abort(403, 'Acesso negado.');
        }   
        return view('oficiosituacoes.create');
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

        $oficioSituacao = $request->all();

        OficioSituacao::create($oficioSituacao); //salva

        Session::flash('create_oficiosituacao', 'Situação do ofício cadastrado com sucesso!');

        return redirect(route('oficiosituacoes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('oficiosituacao.show')) {
            abort(403, 'Acesso negado.');
        }

        $oficiosituacoes = OficioSituacao::findOrFail($id);

        return view('oficiosituacoes.show', compact('oficiosituacoes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('oficiosituacao.edit')) {
            abort(403, 'Acesso negado.');
        }

        $oficiosituacao = OficioSituacao::findOrFail($id);

        return view('oficiosituacoes.edit', compact('oficiosituacao'));
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

        $oficiosituacao = OficioSituacao::findOrFail($id);
            
        $oficiosituacao->update($request->all());
        
        Session::flash('edited_oficiosituacao', 'Situação do ofício alterado com sucesso!');

        return redirect(route('oficiosituacoes.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('oficiosituacao.delete')) {
            abort(403, 'Acesso negado.');
        }

        OficioSituacao::findOrFail($id)->delete();

        Session::flash('deleted_oficiosituacao', 'Situação do ofício excluído com sucesso!');

        return redirect(route('oficiosituacoes.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('oficiosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=SituacoesOficio_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $oficiosituacoes = DB::table('oficio_situacaos');

        $oficiosituacoes = $oficiosituacoes->select('descricao');

        $oficiosituacoes = $oficiosituacoes->orderBy('descricao', 'asc');

        $list = $oficiosituacoes->get()->toArray();

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
        if (Gate::denies('oficiosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $oficiosituacoes = DB::table('oficio_situacaos');

        $oficiosituacoes = $oficiosituacoes->select('descricao');

        $oficiosituacoes = $oficiosituacoes->orderBy('descricao', 'asc');    

        $oficiosituacoes = $oficiosituacoes->get();

        foreach ($oficiosituacoes as $oficiosituacao) {
            $this->pdf->Cell(186, 6, utf8_decode($oficiosituacao->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'OficioSituacao_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }
}
