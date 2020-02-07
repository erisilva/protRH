<?php

namespace App\Http\Controllers;

use App\Grupo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
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
    public function __construct(\App\Reports\GrupoReport $pdf)
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
        if (Gate::denies('grupo.index')) {
            abort(403, 'Acesso negado.');
        }

        $grupos = new Grupo;

        // ordena
        $grupos = $grupos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $grupos = $grupos->paginate(session('perPage', '5'));

        return view('grupos.index', compact('grupos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('grupo.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('grupos.create');
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

        $grupo = $request->all();

        Grupo::create($grupo); //salva

        Session::flash('create_grupo', 'Grupo de trabalho cadastrado com sucesso!');

        return redirect(route('grupos.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('grupo.show')) {
            abort(403, 'Acesso negado.');
        }

        $grupos = Grupo::findOrFail($id);

        return view('grupos.show', compact('grupos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('grupo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $grupo = Grupo::findOrFail($id);

        return view('grupos.edit', compact('grupo'));
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

        $grupo = Grupo::findOrFail($id);
            
        $grupo->update($request->all());
        
        Session::flash('edited_grupo', 'Grupo de trabalho alterado com sucesso!');

        return redirect(route('grupos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            
        if (Gate::denies('grupo.delete')) {
            abort(403, 'Acesso negado.');
        }

        Grupo::findOrFail($id)->delete();

        Session::flash('deleted_grupo', 'Grupo de trabalho excluído com sucesso!');

        return redirect(route('grupos.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('grupo.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=GruposDeTrabalho_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $grupos = DB::table('grupos');

        $grupos = $grupos->select('descricao');

        $grupos = $grupos->orderBy('descricao', 'asc');

        $list = $grupos->get()->toArray();

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
        if (Gate::denies('grupo.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $grupos = DB::table('grupos');

        $grupos = $grupos->select('descricao');

        $grupos = $grupos->orderBy('descricao', 'asc');

        $grupos = $grupos->get();

        foreach ($grupos as $grupo) {
            $this->pdf->Cell(186, 6, utf8_decode($grupo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'OGruposDeTrabalho_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }    
}
