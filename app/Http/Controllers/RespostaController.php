<?php

namespace App\Http\Controllers;

use App\Resposta;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class RespostaController extends Controller
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
    public function __construct(\App\Reports\RespostaReport $pdf)
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
        if (Gate::denies('resposta.index')) {
            abort(403, 'Acesso negado.');
        }

        $respostas = new Resposta;

        // ordena
        $respostas = $respostas->orderBy('descricao', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $respostas = $respostas->paginate(session('perPage', '5'));

        return view('respostas.index', compact('respostas', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('resposta.create')) {
            abort(403, 'Acesso negado.');
        } 
        return view('respostas.create');
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

        $resposta = $request->all();

        Resposta::create($resposta); //salva

        Session::flash('create_resposta', 'Resposta cadastrada com sucesso!');

        return redirect(route('respostas.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('resposta.show')) {
            abort(403, 'Acesso negado.');
        }

        $respostas = Resposta::findOrFail($id);

        return view('respostas.show', compact('respostas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('resposta.edit')) {
            abort(403, 'Acesso negado.');
        }

        $resposta = Resposta::findOrFail($id);

        return view('respostas.edit', compact('resposta'));
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

        $resposta = Resposta::findOrFail($id);
            
        $resposta->update($request->all());
        
        Session::flash('edited_resposta', 'Resposta alterada com sucesso!');

        return redirect(route('respostas.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('resposta.delete')) {
            abort(403, 'Acesso negado.');
        }

        Resposta::findOrFail($id)->delete();

        Session::flash('deleted_resposta', 'Resposta excluída com sucesso!');

        return redirect(route('respostas.index'));
    }

    /*
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('resposta.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Respostas_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $respostas = DB::table('respostas');

        $respostas = $respostas->select('descricao');

        $respostas = $respostas->orderBy('descricao', 'asc');

        $list = $respostas->get()->toArray();

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
        if (Gate::denies('resposta.export')) {
            abort(403, 'Acesso negado.');
        }

        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 12);
        $this->pdf->AddPage();

        $respostas = DB::table('respostas');

        $respostas = $respostas->select('descricao');

        $respostas = $respostas->orderBy('descricao', 'asc');

        $respostas = $respostas->get();

        foreach ($respostas as $resposta) {
            $this->pdf->Cell(186, 6, utf8_decode($resposta->descricao), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'Respostas_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }       
}
