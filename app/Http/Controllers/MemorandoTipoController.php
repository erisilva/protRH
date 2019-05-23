<?php

namespace App\Http\Controllers;

use App\MemorandoTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class MemorandoTipoController extends Controller
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
    public function __construct(\App\Reports\MemorandoTipoReport $pdf)
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
        if (Gate::denies('memorandotipo.index')) {
            abort(403, 'Acesso negado.');
        }

        $memorandotipos = new MemorandoTipo;

        // ordena
        $memorandotipos = $memorandotipos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $memorandotipos = $memorandotipos->paginate(session('perPage', '5'));

        return view('memorandotipos.index', compact('memorandotipos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('memorandotipo.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('memorandotipos.create');
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

        $memorandoTipo = $request->all();

        MemorandoTipo::create($memorandoTipo); //salva

        Session::flash('create_memorandotipo', 'Tipo de memorando cadastrado com sucesso!');

        return redirect(route('memorandotipos.index'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('memorandotipo.show')) {
            abort(403, 'Acesso negado.');
        }

        $memorandotipos = MemorandoTipo::findOrFail($id);

        return view('memorandotipos.show', compact('memorandotipos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('memorandotipo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $memorandotipo = MemorandoTipo::findOrFail($id);

        return view('memorandotipos.edit', compact('memorandotipo'));
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

        $memorandotipo = MemorandoTipo::findOrFail($id);
            
        $memorandotipo->update($request->all());
        
        Session::flash('edited_memorandotipo', 'Tipo de memorando alterado com sucesso!');

        return redirect(route('memorandotipos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('memorandotipo.delete')) {
            abort(403, 'Acesso negado.');
        }

        MemorandoTipo::findOrFail($id)->delete();

        Session::flash('deleted_memorandotipo', 'Tipo de memorando excluído com sucesso!');

        return redirect(route('memorandotipos.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('memorandotipo.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=TiposMemorandos_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $memorandotipos = DB::table('memorando_tipos');

        $memorandotipos = $memorandotipos->select('descricao');

        $memorandotipos = $memorandotipos->orderBy('descricao', 'asc');

        $list = $memorandotipos->get()->toArray();

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
        if (Gate::denies('memorandotipo.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $memorandotipos = DB::table('memorando_tipos');

        $memorandotipos = $memorandotipos->select('descricao');

        $memorandotipos = $memorandotipos->orderBy('descricao', 'asc');    

        $memorandotipos = $memorandotipos->get();

        foreach ($memorandotipos as $memorandotipo) {
            $this->pdf->Cell(186, 6, utf8_decode($memorandotipo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'MemorandoTipos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }
}
