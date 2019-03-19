<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Auth;

class ChangePasswordController extends Controller
{
    /**
     * Construtor.
     *
     * precisa estar logado ao sistema
     * precisa ter a conta ativa (access)
     *
     * @return 
     */
    public function __construct()
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);
    }

	/**
     * Retorna o fomulário de alteração da senha do operador
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function showPasswordUpdateForm(){
        return view('admin.users.password');
    }

    /**
     * Atualiza a senha do operador
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function passwordUpdate(Request $request){
		$this->validate($request, [
			'password' => 'required|min:6',
          	'newpassword' => 'required|min:6|confirmed',

        ]);

        $input = $request->all();
        $user = Auth::user();

        /* verifica se a senha passada confere com a senha do usuário logado */
        if (Auth::attempt(['email' => $user->email, 'password' => $input['password'] ])) {
            /*  criptografa e atualiza a senha */           
            $user->password = Hash::make($input['newpassword']);
            $user->update();
            Session::flash('password_altered', 'Senha Alterada!');           
        } else {
            Session::flash('password_wrong', 'Senha Atual Incorreta!');
        }      

        return view('admin.users.password');
    }
}
