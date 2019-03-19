<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // estou iniciando a sessão que defini a quantidade de linhas
        // exibidas por páginas aqui, ainda em teste
        session(['perPage' => 5]);
        return view('home');
    }
}
