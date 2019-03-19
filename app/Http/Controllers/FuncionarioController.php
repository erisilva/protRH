<?php

namespace App\Http\Controllers;

use App\Funcionario;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class FuncionarioController extends Controller
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
    public function __construct(\App\Reports\FuncionarioReport $pdf)
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
        if (Gate::denies('funcionario.index')) {
            abort(403, 'Acesso negado.');
        }

        $funcionarios = new Funcionario;

        // filtros
        if (request()->has('nome')){
            $funcionarios = $funcionarios->where('nome', 'like', '%' . request('nome') . '%');
        }

        if (request()->has('matricula')){
            $funcionarios = $funcionarios->where('matricula', 'like', '%' . request('matricula') . '%');
        }

        // ordena
        $funcionarios = $funcionarios->orderBy('nome', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $funcionarios = $funcionarios->paginate(session('perPage', '5'))->appends([          
            'nome' => request('nome'),
            'matricula' => request('matricula'),           
            ]);

        return view('funcionarios.index', compact('funcionarios', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('funcionario.create')) {
            abort(403, 'Acesso negado.');
        }

        return view('funcionarios.create');
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
          'nome' => 'required',
          'matricula' => 'required',
        ]);

        $funcionario = $request->all();

        Funcionario::create($funcionario); //salva

        Session::flash('create_funcionario', 'Funcionário cadastrado com sucesso!');

        return redirect(route('funcionarios.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('funcionario.show')) {
            abort(403, 'Acesso negado.');
        }

        $funcionario = Funcionario::findOrFail($id);

        return view('funcionarios.show', compact('funcionario'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('funcionario.edit')) {
            abort(403, 'Acesso negado.');
        }

        $funcionario = Funcionario::findOrFail($id);

        return view('funcionarios.edit', compact('funcionario'));
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
          'nome' => 'required',
          'matricula' => 'required',
        ]);

        $funcionario = Funcionario::findOrFail($id);
            
        $funcionario->update($request->all());
        
        Session::flash('edited_funcionario', 'Funcionário alterado com sucesso!');

        return redirect(route('funcionarios.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('funcionario.delete')) {
            abort(403, 'Acesso negado.');
        }

        Funcionario::findOrFail($id)->delete();

        Session::flash('deleted_funcionario', 'Funcionário excluído com sucesso!');

        return redirect(route('funcionarios.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('funcionario.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Funcionarios_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $funcionarios = DB::table('funcionarios');

        $funcionarios = $funcionarios->select('nome', 'matricula', 'email', 'numeropasta');

        // filtros
        if (request()->has('nome')){
            $funcionarios = $funcionarios->where('nome', 'like', '%' . request('nome') . '%');
        }

        if (request()->has('matricula')){
            $funcionarios = $funcionarios->where('matricula', 'like', '%' . request('matricula') . '%');
        }

        $funcionarios = $funcionarios->orderBy('nome', 'asc');

        $list = $funcionarios->get()->toArray();

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
        if (Gate::denies('funcionario.export')) {
            abort(403, 'Acesso negado.');
        }
                
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(12, 10, 12);
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->AddPage();

        $funcionarios = DB::table('funcionarios');

        $funcionarios = $funcionarios->select('nome', 'matricula', 'email', 'numeropasta');

        // filtros
        if (request()->has('nome')){
            $funcionarios = $funcionarios->where('nome', 'like', '%' . request('nome') . '%');
        }

        if (request()->has('matricula')){
            $funcionarios = $funcionarios->where('matricula', 'like', '%' . request('matricula') . '%');
        }

        $funcionarios = $funcionarios->orderBy('nome', 'asc');    


        $funcionarios = $funcionarios->get();

        foreach ($funcionarios as $funcionario) {
            $this->pdf->Cell(76, 6, utf8_decode($funcionario->nome), 0, 0,'L');
            $this->pdf->Cell(30, 6, utf8_decode($funcionario->matricula), 0, 0,'L');
            $this->pdf->Cell(65, 6, utf8_decode($funcionario->email), 0, 0,'L');
            $this->pdf->Cell(15, 6, utf8_decode($funcionario->numeropasta), 0, 0,'R');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'Funcionários_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    }
}
