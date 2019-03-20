<?php

namespace App\Http\Controllers;

use App\PeriodoTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class PeriodoTipoController extends Controller
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
    public function __construct(\App\Reports\PeriodoTipoReport $pdf)
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
        if (Gate::denies('periodotipo.index')) {
            abort(403, 'Acesso negado.');
        }

        $periodotipos = new PeriodoTipo;

        // ordena
        $periodotipos = $periodotipos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $periodotipos = $periodotipos->paginate(session('perPage', '5'));

        return view('periodotipos.index', compact('periodotipos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('periodotipo.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('periodotipos.create');
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

        $PeriodoTipo = $request->all();

        PeriodoTipo::create($PeriodoTipo); //salva

        Session::flash('create_periodotipo', 'Tipo de período cadastrado com sucesso!');

        return redirect(route('periodotipos.index'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('periodotipo.show')) {
            abort(403, 'Acesso negado.');
        }

        $periodotipos = PeriodoTipo::findOrFail($id);

        return view('periodotipos.show', compact('periodotipos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('periodotipo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $periodotipo = PeriodoTipo::findOrFail($id);

        return view('periodotipos.edit', compact('periodotipo'));
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

        $periodotipo = PeriodoTipo::findOrFail($id);
            
        $periodotipo->update($request->all());
        
        Session::flash('edited_protocolotipo', 'Tipo de período alterado com sucesso!');

        return redirect(route('periodotipos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('periodotipo.delete')) {
            abort(403, 'Acesso negado.');
        }

        PeriodoTipo::findOrFail($id)->delete();

        Session::flash('deleted_periodotipo', 'Tipo de período excluído com sucesso!');

        return redirect(route('periodotipos.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('periodotipo.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=TiposPeríodo_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $periodotipos = DB::table('periodo_tipos');

        $periodotipos = $periodotipos->select('descricao');

        $periodotipos = $periodotipos->orderBy('descricao', 'asc');

        $list = $periodotipos->get()->toArray();

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
        if (Gate::denies('periodotipo.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $periodotipos = DB::table('periodo_tipos');

        $periodotipos = $periodotipos->select('descricao');


        $periodotipos = $periodotipos->orderBy('descricao', 'asc');    


        $periodotipos = $periodotipos->get();

        foreach ($periodotipos as $periodotipo) {
            $this->pdf->Cell(186, 6, utf8_decode($periodotipo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'PeríodoTipos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }      
}
