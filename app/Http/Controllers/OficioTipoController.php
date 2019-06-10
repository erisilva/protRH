<?php

namespace App\Http\Controllers;

use App\OficioTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class OficioTipoController extends Controller
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
    public function __construct(\App\Reports\OficioTipoReport $pdf)
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
        if (Gate::denies('oficiotipo.index')) {
            abort(403, 'Acesso negado.');
        }

        $oficiotipos = new OficioTipo;

        // ordena
        $oficiotipos = $oficiotipos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $oficiotipos = $oficiotipos->paginate(session('perPage', '5'));

        return view('oficiotipos.index', compact('oficiotipos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('oficiotipo.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('oficiotipos.create');
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

        $oficioTipo = $request->all();

        OficioTipo::create($oficioTipo); //salva

        Session::flash('create_oficiotipo', 'Tipo de ofício cadastrado com sucesso!');

        return redirect(route('oficiotipos.index'));  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('oficiotipo.show')) {
            abort(403, 'Acesso negado.');
        }

        $oficiotipos = oficioTipo::findOrFail($id);

        return view('oficiotipos.show', compact('oficiotipos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('oficiotipo.edit')) {
            abort(403, 'Acesso negado.');
        }

        $oficiotipo = OficioTipo::findOrFail($id);

        return view('oficiotipos.edit', compact('oficiotipo'));
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

        $oficiotipo = OficioTipo::findOrFail($id);
            
        $oficiotipo->update($request->all());
        
        Session::flash('edited_oficiotipo', 'Tipo de ofício alterado com sucesso!');

        return redirect(route('oficiotipos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('oficiotipo.delete')) {
            abort(403, 'Acesso negado.');
        }

        OficioTipo::findOrFail($id)->delete();

        Session::flash('deleted_oficiotipo', 'Tipo de ofício excluído com sucesso!');

        return redirect(route('oficiotipos.index'));
    }


    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('oficiotipo.export')) {
            abort(403, 'Acesso negado.');
        }

       $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=TiposOficios_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $oficiotipos = DB::table('oficio_tipos');

        $oficiotipos = $oficiotipos->select('descricao');

        $oficiotipos = $oficiotipos->orderBy('descricao', 'asc');

        $list = $oficiotipos->get()->toArray();

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
        if (Gate::denies('oficiotipo.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $oficiotipos = DB::table('oficio_tipos');

        $oficiotipos = $oficiotipos->select('descricao');

        $oficiotipos = $oficiotipos->orderBy('descricao', 'asc');    

        $oficiotipos = $oficiotipos->get();

        foreach ($oficiotipos as $oficiotipo) {
            $this->pdf->Cell(186, 6, utf8_decode($oficiotipo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'OficioTipos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }
}
