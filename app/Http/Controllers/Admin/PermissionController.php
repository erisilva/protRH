<?php

namespace App\Http\Controllers\Admin;

use App\Permission;
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    protected $pdf; // usado para injeção do cabeçalho e rodapé da impressão pelo fpdf

    /**
     * Construtor.
     *
     * precisa estar logado ao sistema
     * precisa ter a conta ativa (access)
     *
     * @return 
     */
    public function __construct(\App\Reports\PermissionReport $pdf)// o modelo que é usado para extender o FPDF
                                                              // é colocado em /App/reports
                                                              // nesse arquivo é desenhado o cabeçalho e
                                                              // rodapé das páginas de saída 
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
        if (Gate::denies('permission.index')) {
            abort(403, 'Acesso negado.');
        }

        $permissions = new Permission;

        // filtros
        if (request()->has('name')){
            $permissions = $permissions->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $permissions = $permissions->where('description', 'like', '%' . request('description') . '%');
        }

        // ordena
        $permissions = $permissions->orderBy('name', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $permissions = $permissions->paginate(session('perPage', '5'))->appends([          
            'name' => request('name'),
            'description' => request('description'),           
            ]);

        return view('admin.permissions.index', compact('permissions', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('permission.create')) {
            abort(403, 'Acesso negado.');
        }

        return view('admin.permissions.create');
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
          'name' => 'required',
          'description' => 'required',
        ]);

        $permission = $request->all();

        Permission::create($permission); //salva

        Session::flash('create_permission', 'Permissão cadastrada com sucesso!');

        return redirect(route('permissions.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('permission.show')) {
            abort(403, 'Acesso negado.');
        }

        // permissão que será exibido e pode ser excluido
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('permission.edit')) {
            abort(403, 'Acesso negado.');
        }

        // usuário que será alterado
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
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
          'name' => 'required',
          'description' => 'required',
        ]);

        $permission = Permission::findOrFail($id);
            
        $permission->update($request->all());
        
        Session::flash('edited_permission', 'Permissão alterada com sucesso!');

        return redirect(route('permissions.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('permission.delete')) {
            abort(403, 'Acesso negado.');
        }

        Permission::findOrFail($id)->delete();

        Session::flash('deleted_permission', 'Permissão excluída com sucesso!');

        return redirect(route('permissions.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('permission.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Permissões_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $permissions = DB::table('permissions');

        $permissions = $permissions->select('name', 'description');

        // filtros
        if (request()->has('name')){
            $permissions = $permissions->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $permissions = $permissions->where('description', 'like', '%' . request('description') . '%');
        }

        $permissions = $permissions->orderBy('name', 'asc');

        $list = $permissions->get()->toArray();

        // nota: mostra consulta gerada pelo elloquent
        // dd($distritos->toSql());

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
        if (Gate::denies('permission.export')) {
            abort(403, 'Acesso negado.');
        }
        
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetFont('Arial','',12);
        $this->pdf->AddPage();

        $permissions = DB::table('permissions');

        $permissions = $permissions->select('name', 'description');

        // filtros
        if (request()->has('name')){
            $permissions = $permissions->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $permissions = $permissions->where('description', 'like', '%' . request('description') . '%');
        }

        $permissions = $permissions->orderBy('name', 'asc');    


        $permissions = $permissions->get();

        foreach ($permissions as $permission) {
            $this->pdf->Cell(80, 6, utf8_decode($permission->name), 0, 0,'L');
            $this->pdf->Cell(106, 6, utf8_decode($permission->description), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'Permissões_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    } 
}
