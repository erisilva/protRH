<?php

namespace App\Http\Controllers;

use App\ProtocoloSituacao;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class ProtocoloSituacaoController extends Controller
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
    public function __construct(\App\Reports\ProtocoloSituacaoReport $pdf)
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
        if (Gate::denies('protocolosituacao.index')) {
            abort(403, 'Acesso negado.');
        }

        $protocolosituacoes = new ProtocoloSituacao;

        // ordena
        $protocolosituacoes = $protocolosituacoes->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $protocolosituacoes = $protocolosituacoes->paginate(session('perPage', '5'));

        return view('protocolosituacoes.index', compact('protocolosituacoes', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('protocolosituacao.create')) {
            abort(403, 'Acesso negado.');
        }   
        return view('protocolosituacoes.create');
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

        $ProtocoloSituacao = $request->all();

        ProtocoloSituacao::create($ProtocoloSituacao); //salva

        Session::flash('create_protocolosituacao', 'Situação do protocolo cadastrado com sucesso!');

        return redirect(route('protocolosituacoes.index'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('protocolosituacao.show')) {
            abort(403, 'Acesso negado.');
        }

        $protocolosituacoes = ProtocoloSituacao::findOrFail($id);

        return view('protocolosituacoes.show', compact('protocolosituacoes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('protocolosituacao.edit')) {
            abort(403, 'Acesso negado.');
        }

        $protocolosituacao = ProtocoloSituacao::findOrFail($id);

        return view('protocolosituacoes.edit', compact('protocolosituacao'));
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

        $protocolosituacao = ProtocoloSituacao::findOrFail($id);
            
        $protocolosituacao->update($request->all());
        
        Session::flash('edited_protocolosituacao', 'Situação do protocolo alterado com sucesso!');

        return redirect(route('protocolosituacoes.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('protocolosituacao.delete')) {
            abort(403, 'Acesso negado.');
        }

        ProtocoloSituacao::findOrFail($id)->delete();

        Session::flash('deleted_protocolosituacao', 'Situação do protocolo excluído com sucesso!');

        return redirect(route('protocolosituacoes.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('protocolosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=SituacoesProtocolo_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $protocolosituacoes = DB::table('protocolo_situacaos');

        $protocolosituacoes = $protocolosituacoes->select('descricao');

        $protocolosituacoes = $protocolosituacoes->orderBy('descricao', 'asc');

        $list = $protocolosituacoes->get()->toArray();

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
        if (Gate::denies('protocolosituacao.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $protocolosituacoes = DB::table('protocolo_situacaos');

        $protocolosituacoes = $protocolosituacoes->select('descricao');


        $protocolosituacoes = $protocolosituacoes->orderBy('descricao', 'asc');    


        $protocolosituacoes = $protocolosituacoes->get();

        foreach ($protocolosituacoes as $protocolosituacao) {
            $this->pdf->Cell(186, 6, utf8_decode($protocolosituacao->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'ProtocoloSituacao_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }      
}
