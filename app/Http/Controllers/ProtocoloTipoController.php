<?php

namespace App\Http\Controllers;

use App\ProtocoloTipo;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class ProtocoloTipoController extends Controller
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
    public function __construct(\App\Reports\ProtocoloTipoReport $pdf)
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
        $protocolotipos = new ProtocoloTipo;

        // ordena
        $protocolotipos = $protocolotipos->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $protocolotipos = $protocolotipos->paginate(session('perPage', '5'));

        return view('protocolotipos.index', compact('protocolotipos', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('protocolotipos.create');
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

        $ProtocoloTipo = $request->all();

        ProtocoloTipo::create($ProtocoloTipo); //salva

        Session::flash('create_protocolotipo', 'Tipo de protocolo cadastrado com sucesso!');

        return redirect(route('protocolotipos.index'));        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $protocolotipos = ProtocoloTipo::findOrFail($id);

        return view('protocolotipos.show', compact('protocolotipos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $protocolotipo = ProtocoloTipo::findOrFail($id);

        return view('protocolotipos.edit', compact('protocolotipo'));
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

        $protocolotipo = ProtocoloTipo::findOrFail($id);
            
        $protocolotipo->update($request->all());
        
        Session::flash('edited_protocolotipo', 'Tipo de protocolo alterado com sucesso!');

        return redirect(route('protocolotipos.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProtocoloTipo::findOrFail($id)->delete();

        Session::flash('deleted_protocolotipo', 'Tipo de protocolo excluído com sucesso!');

        return redirect(route('protocolotipos.index'));
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
            ,   'Content-Disposition' => 'attachment; filename=TiposProtocolo_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $protocolotipos = DB::table('protocolo_tipos');

        $protocolotipos = $protocolotipos->select('descricao');

        $protocolotipos = $protocolotipos->orderBy('descricao', 'asc');

        $list = $protocolotipos->get()->toArray();

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
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $protocolotipos = DB::table('protocolo_tipos');

        $protocolotipos = $protocolotipos->select('descricao');


        $protocolotipos = $protocolotipos->orderBy('descricao', 'asc');    


        $protocolotipos = $protocolotipos->get();

        foreach ($protocolotipos as $protocolotipo) {
            $this->pdf->Cell(186, 6, utf8_decode($protocolotipo->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'ProtocoloTipos_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;

    }   
}
