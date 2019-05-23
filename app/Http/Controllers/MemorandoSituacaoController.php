<?php

namespace App\Http\Controllers;

use App\MemorandoSituacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class MemorandoSituacaoController extends Controller
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
    public function __construct(\App\Reports\MemorandoSituacaoReport $pdf)
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
        if (Gate::denies('memorandosituacao.index')) {
            abort(403, 'Acesso negado.');
        }

        $memorandosituacoes = new MemorandoSituacao;

        // ordena
        $memorandosituacoes = $memorandosituacoes->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $memorandosituacoes = $memorandosituacoes->paginate(session('perPage', '5'));

        return view('memorandosituacoes.index', compact('memorandosituacoes', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('memorandosituacao.create')) {
            abort(403, 'Acesso negado.');
        }   
        return view('memorandosituacoes.create');
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

        $memorandoSituacao = $request->all();

        MemorandoSituacao::create($memorandoSituacao); //salva

        Session::flash('create_memorandosituacao', 'Situação do memorando cadastrado com sucesso!');

        return redirect(route('memorandosituacoes.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('memorandosituacao.show')) {
            abort(403, 'Acesso negado.');
        }

        $memorandosituacoes = MemorandoSituacao::findOrFail($id);

        return view('memorandosituacoes.show', compact('memorandosituacoes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('memorandosituacao.edit')) {
            abort(403, 'Acesso negado.');
        }

        $memorandosituacao = MemorandoSituacao::findOrFail($id);

        return view('memorandosituacoes.edit', compact('memorandosituacao'));
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

        $memorandosituacao = MemorandoSituacao::findOrFail($id);
            
        $memorandosituacao->update($request->all());
        
        Session::flash('edited_memorandosituacao', 'Situação do memorando alterado com sucesso!');

        return redirect(route('memorandosituacoes.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('memorandosituacao.delete')) {
            abort(403, 'Acesso negado.');
        }

        MemorandoSituacao::findOrFail($id)->delete();

        Session::flash('deleted_memorandosituacao', 'Situação do memorando excluído com sucesso!');

        return redirect(route('memorandosituacoes.index'));
    }


    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('memorandosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=SituacoesMemorando_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $memorandosituacoes = DB::table('memorando_situacaos');

        $memorandosituacoes = $memorandosituacoes->select('descricao');

        $memorandosituacoes = $memorandosituacoes->orderBy('descricao', 'asc');

        $list = $memorandosituacoes->get()->toArray();

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
        if (Gate::denies('memorandosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $memorandosituacoes = DB::table('memorando_situacaos');

        $memorandosituacoes = $memorandosituacoes->select('descricao');

        $memorandosituacoes = $memorandosituacoes->orderBy('descricao', 'asc');    

        $memorandosituacoes = $memorandosituacoes->get();

        foreach ($memorandosituacoes as $memorandosituacao) {
            $this->pdf->Cell(186, 6, utf8_decode($memorandosituacao->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'MemorandoSituacao_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }          
}
