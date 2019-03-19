<?php

namespace App\Http\Controllers;

use App\Setor;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class SetorController extends Controller
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
    public function __construct(\App\Reports\SetorReport $pdf)
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
        if (Gate::denies('setor.index')) {
            abort(403, 'Acesso negado.');
        }

        $setores = new Setor;

        // filtros
        if (request()->has('codigo')){
            $setores = $setores->where('codigo', 'like', '%' . request('codigo') . '%');
        }

        if (request()->has('descricao')){
            $setores = $setores->where('descricao', 'like', '%' . request('descricao') . '%');
        }

        // ordena
        $setores = $setores->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $setores = $setores->paginate(session('perPage', '5'))->appends([          
            'codigo' => request('codigo'),
            'descricao' => request('descricao'),           
            ]);

        return view('setores.index', compact('setores', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('setor.create')) {
            abort(403, 'Acesso negado.');
        }

        return view('setores.create');
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
          'codigo' => 'required',
          'descricao' => 'required',
        ]);

        $setor = $request->all();

        Setor::create($setor); //salva

        Session::flash('create_setor', 'Setor cadastrado com sucesso!');

        return redirect(route('setores.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('setor.show')) {
            abort(403, 'Acesso negado.');
        }

        $setor = Setor::findOrFail($id);

        return view('setores.show', compact('setor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('setor.edit')) {
            abort(403, 'Acesso negado.');
        }

        $setor = Setor::findOrFail($id);

        return view('setores.edit', compact('setor'));
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
          'codigo' => 'required',
          'descricao' => 'required',
        ]);

        $setor = Setor::findOrFail($id);
            
        $setor->update($request->all());
        
        Session::flash('edited_setor', 'Setor alterado com sucesso!');

        return redirect(route('setores.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('setor.delete')) {
            abort(403, 'Acesso negado.');
        }

        Setor::findOrFail($id)->delete();

        Session::flash('deleted_setor', 'Setor excluído com sucesso!');

        return redirect(route('setores.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('setor.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Setores_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $setores = DB::table('setors');

        $setores = $setores->select('codigo', 'descricao');

        // filtros
        if (request()->has('codigo')){
            $setores = $setores->where('codigo', 'like', '%' . request('codigo') . '%');
        }

        if (request()->has('descricao')){
            $setores = $setores->where('descricao', 'like', '%' . request('descricao') . '%');
        }

        $setores = $setores->orderBy('codigo', 'asc');

        $list = $setores->get()->toArray();

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
        if (Gate::denies('setor.export')) {
            abort(403, 'Acesso negado.');
        }


        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $setores = DB::table('setors');

        $setores = $setores->select('codigo', 'descricao');

        // filtros
        if (request()->has('codigo')){
            $setores = $setores->where('codigo', 'like', '%' . request('codigo') . '%');
        }

        if (request()->has('descricao')){
            $setores = $setores->where('descricao', 'like', '%' . request('descricao') . '%');
        }

        $setores = $setores->orderBy('codigo', 'asc');    


        $setores = $setores->get();

        foreach ($setores as $setor) {
            $this->pdf->Cell(76, 6, utf8_decode($setor->codigo), 0, 0,'L');
            $this->pdf->Cell(110, 6, utf8_decode($setor->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'Setores_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }    
}
