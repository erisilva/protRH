<?php

namespace App\Http\Controllers;

use App\Periodo;
use App\protocolo;
use App\ProtocoloSituacao;
use App\ProtocoloTipo;
use App\PeriodoTipo;

use Response;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon; // tratamento de datas
use Illuminate\Support\Facades\Redirect; // para poder usar o redirect

class PeriodoController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'periodo_tipo_id' => 'required',
        ]);

        $input_periodo = $request->all();

        if ($input_periodo['dtainicio'] != ""){
            $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtainicio'))->format('Y-m-d');           
            $input_periodo['inicio'] =  $dataFormatadaMysql;            
        }

        if ($input_periodo['dtafinal'] != ""){
            $dataFormatadaMysql = Carbon::createFromFormat('d/m/Y', request('dtafinal'))->format('Y-m-d');           
            $input_periodo['fim'] =  $dataFormatadaMysql;            
        }            

        Periodo::create($input_periodo); //salva

        $periodos = Periodo::where('protocolo_id', '=', $input_periodo['protocolo_id'])->orderBy('id', 'asc')->get();

        $protocolo = Protocolo::find($input_periodo['protocolo_id']);

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();

        Session::flash('create_periodo', 'Período inserido com sucesso!');        

        return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $periodo = Periodo::findOrFail($id);

        $periodos = Periodo::where('protocolo_id', '=', $periodo->protocolo_id)->orderBy('id', 'asc')->get();

        $protocolo = Protocolo::find($periodo->protocolo_id);

        $protocolosituacoes = ProtocoloSituacao::orderBy('id', 'asc')->get();

        $protocolotipos = ProtocoloTipo::orderBy('descricao', 'asc')->get();

        $periodotipos = PeriodoTipo::orderBy('descricao', 'asc')->get();

        $periodo->delete();        

        Session::flash('delete_periodo', 'Período excluído com sucesso!');

        return Redirect::route('protocolos.edit', $protocolo->id)->with('protocolo', 'protocolosituacoes', 'protocolotipos', 'periodotipos', 'periodos');
    }
}
