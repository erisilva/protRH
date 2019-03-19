<?php

namespace App\Http\Controllers\Admin;

use App\Role; // Perfil
use App\Permission; // Permissões
use App\Perpage;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
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
    public function __construct(\App\Reports\RoleReport $pdf) // o modelo que é usado para extender o FPDF
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
        if (Gate::denies('role.index')) {
            abort(403, 'Acesso negado.');
        }

        $roles = new Role;

        // filtros
        if (request()->has('name')){
            $roles = $roles->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $roles = $roles->where('description', 'like', '%' . request('description') . '%');
        }            
        // ordena
        $roles = $roles->orderBy('name', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $roles = $roles->paginate(session('perPage', '5'))->appends([          
            'name' => request('name'),
            'description' => request('description'),           
            ]);

        return view('admin.roles.index', compact('roles', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('role.create')) {
            abort(403, 'Acesso negado.');
        }

        // listagem de perfis (roles)
        $permissions = Permission::orderBy('name','asc')->get();

        return view('admin.roles.create', compact('permissions'));
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

        $role = $request->all();

        $newRole = Role::create($role); //salva

        // salva os perfis (roles)
        if(isset($role['permissions']) && count($role['permissions'])){
            foreach ($role['permissions'] as $key => $value) {
                $newRole->permissions()->attach($value);
            }

        } 

        Session::flash('create_role', 'Perfil cadastrado com sucesso!');

        return redirect(route('roles.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('role.show')) {
            abort(403, 'Acesso negado.');
        }

        // perfil que será exibido e pode ser excluido
        $role = Role::findOrFail($id);

        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::denies('role.edit')) {
            abort(403, 'Acesso negado.');
        }

        // perfil que será alterado
        $role = Role::findOrFail($id);

        // listagem de perfis (roles)
        $permissions = Permission::orderBy('name','asc')->get();

        return view('admin.roles.edit', compact('role', 'permissions'));
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

        $role = Role::findOrFail($id);

        // recebe todos valores entrados no formulário
        $input = $request->all();

        // remove todos as permissões vinculadas a esse operador
        $permissions = $role->permissions;
        if(count($permissions)){
            foreach ($permissions as $key => $value) {
               $role->permissions()->detach($value->id);
            }
        }

        // vincula os novas permissões desse operador
        if(isset($input['permissions']) && count($input['permissions'])){
            foreach ($input['permissions'] as $key => $value) {
               $role->permissions()->attach($value);
            }
        }
            
        $role->update($input);
        
        Session::flash('edited_role', 'Perfil alterado com sucesso!');

        return redirect(route('roles.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('role.delete')) {
            abort(403, 'Acesso negado.');
        }

        Role::findOrFail($id)->delete();

        Session::flash('deleted_role', 'Permissão excluída com sucesso!');

        return redirect(route('roles.index'));
    }

    /**
     * Exportação para planilha (csv)
     *
     * @param  int  $id
     * @return Response::stream()
     */
    public function exportcsv()
    {
        if (Gate::denies('role.export')) {
            abort(403, 'Acesso negado.');
        }

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=Perfis_' .  date("Y-m-d H:i:s") . '.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $roles = DB::table('roles');

        $roles = $roles->select('name', 'description');

        // filtros
        if (request()->has('name')){
            $roles = $roles->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $roles = $roles->where('description', 'like', '%' . request('description') . '%');
        }

        $roles = $roles->orderBy('name', 'asc');

        $list = $roles->get()->toArray();

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
        if (Gate::denies('role.export')) {
            abort(403, 'Acesso negado.');
        }
        
        $this->pdf->AliasNbPages();   
        $this->pdf->SetMargins(15, 15, 15);
        $this->pdf->SetFont('Arial','',12);
        $this->pdf->AddPage();

        $roles = DB::table('roles');

        $roles = $roles->select('name', 'description');

        // filtros
        if (request()->has('name')){
            $roles = $roles->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $roles = $roles->where('description', 'like', '%' . request('description') . '%');
        }

        $roles = $roles->orderBy('name', 'asc');    


        $roles = $roles->get();

        foreach ($roles as $role) {
            $this->pdf->Cell(80, 6, utf8_decode($role->name), 0, 0,'L');
            $this->pdf->Cell(106, 6, utf8_decode($role->description), 0, 0,'L');
            $this->pdf->Ln();
        }

        $this->pdf->Output('D', 'Perfis_' .  date("Y-m-d H:i:s") . '.pdf', true);
        exit;
    } 
}
