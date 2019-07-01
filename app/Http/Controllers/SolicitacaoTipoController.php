<?php

namespace App\Http\Controllers;

use App\SolicitacaoTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class SolicitacaoTipoController extends Controller
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
    public function __construct(\App\Reports\SolicitacaoTipoReport $pdf)
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
        if (Gate::denies('solicitacaotipo.index')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaotipos = new SolicitacaoTipo;

        // ordena
        $solicitacaotipos = $solicitacaotipos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $solicitacaotipos = $solicitacaotipos->paginate(session('perPage', '5'));

        return view('solicitacaotipos.index', compact('solicitacaotipos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('solicitacaotipo.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('solicitacaotipos.create');
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

        $solicitacaoTipo = $request->all();

        SolicitacaoTipo::create($solicitacaoTipo); //salva

        Session::flash('create_solicitacaotipo', 'Tipo de solicitação cadastrado com sucesso!');

        return redirect(route('solicitacaotipos.index')); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('solicitacaotipo.show')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaotipos = SolicitacaoTipo::findOrFail($id);

        return view('solicitacaotipos.show', compact('solicitacaotipos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('solicitacaotipo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $solicitacaotipo = SolicitacaoTipo::findOrFail($id);

        return view('solicitacaotipos.edit', compact('solicitacaotipo'));
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

        $solicitacaotipo = SolicitacaoTipo::findOrFail($id);
            
        $solicitacaotipo->update($request->all());
        
        Session::flash('edited_solicitacaotipo', 'Tipo de solicitação alterado com sucesso!');

        return redirect(route('solicitacaotipos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('solicitacaotipo.delete')) {
            abort(403, 'Acesso negado.');
        }

        SolicitacaoTipo::findOrFail($id)->delete();

        Session::flash('deleted_solicitacaotipo', 'Tipo de solicitação excluído com sucesso!');

        return redirect(route('solicitacaotipos.index'));
    }


    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('solicitacaotipo.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=TiposSolicitações_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $solicitacaotipos = DB::table('solicitacao_tipos');

        $solicitacaotipos = $solicitacaotipos->select('descricao');

        $solicitacaotipos = $solicitacaotipos->orderBy('descricao', 'asc');

        $list = $solicitacaotipos->get()->toArray();

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
        if (Gate::denies('solicitacaotipo.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $solicitacaotipos = DB::table('solicitacao_tipos');

        $solicitacaotipos = $solicitacaotipos->select('descricao');

        $solicitacaotipos = $solicitacaotipos->orderBy('descricao', 'asc');    

        $solicitacaotipos = $solicitacaotipos->get();

        foreach ($solicitacaotipos as $solicitacaotipo) {
            $this->pdf->Cell(186, 6, utf8_decode($solicitacaotipo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'SolicitaçãoTipos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }        
}
