@extends('layouts.app')

@section('css-header')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('protocolos.index') }}">Lista de Protocolos</a></li>
    </ol>
  </nav>
  @if(Session::has('deleted_protocolo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('deleted_protocolo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if ($errors->has('dtaencaminhamento'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Erro!</strong>  {{ $errors->first('dtaencaminhamento') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif 
  @if ($errors->has('turnoencaminhamento'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Erro!</strong>  {{ $errors->first('turnoencaminhamento') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif 
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route('protocolos.create') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-plus-square"></i> Novo Registro</a>
    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
    <div class="btn-group" role="group">
      <button id="btnGroupDropOptions" type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-print"></i> Relatórios
      </button>
      <div class="dropdown-menu" aria-labelledby="btnGroupDropOptions">
        <a class="dropdown-item" href="#" id="btnExportarCSV"><i class="fas fa-file-download"></i> Exportar Planilha</a>
        <a class="dropdown-item" href="#" id="btnExportarPDF"><i class="fas fa-file-download"></i> Exportar PDF</a>
        <a class="dropdown-item" href="#" id="btnExportarPDFporsetor"><i class="fas fa-file-download"></i> Exportar PDF por Setor</a>
        <a class="dropdown-item" href="#" id="btnExportarPDFporsetorsimples"><i class="fas fa-file-download"></i> Exportar PDF por Setor (Simples)</a>
        <a class="dropdown-item" href="#" id="btnExportarEncaminhamento" data-toggle="modal" data-target="#modalEncaminhar"><i class="fas fa-file-download"></i> Encaminhamentos Diários</a>
      </div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Nº</th>
                <th scope="col">Dia</th>
                <th scope="col">Hora</th>
                <th scope="col">Funcionario</th>
                <th scope="col">Setor</th>
                <th scope="col">Tipo</th>
                <th scope="col">Situação</th>
                <th scope="col">Grupo</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($protocolos as $protocolo)
            <tr>
                <td><b>{{ $protocolo->id }}</b></td>
                <td>{{ $protocolo->created_at->format('d/m/Y') }}</td>
                <td>{{ $protocolo->created_at->format('H:i') }}</td>
                <td>{{ $protocolo->funcionario->nome }}</td>
                <td>{{ $protocolo->setor->descricao }}</td>
                <td>{{ $protocolo->protocoloTipo->descricao }}</td>
                <td>{{ $protocolo->protocoloSituacao->descricao }}</td>
                <td>{{ $protocolo->grupo->descricao }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('protocolos.edit', $protocolo->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-edit"></i></a>
                    <a href="{{route('protocolos.show', $protocolo->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('protocolos.export.pdf.individual', $protocolo->id) }}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-print"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $protocolos->currentPage() }} de {{ $protocolos->lastPage() }}. Total de registros: {{ $protocolos->total() }}.</p>
  <div class="container-fluid">
      {{ $protocolos->links() }}
  </div>
  <!-- Janela de filtragem da consulta -->
  <div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="JanelaFiltro" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-filter"></i> Filtro</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Filtragem dos dados -->
          <form method="GET" action="{{ route('protocolos.index') }}">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="numprotocolo">Nº</label>
                <input type="text" class="form-control" id="numprotocolo" name="numprotocolo" value="{{request()->input('numprotocolo')}}">
              </div>
              <div class="form-group col-md-4">
                <label for="dtainicio">Data inicial</label>
                <input type="text" class="form-control" id="dtainicio" name="dtainicio" value="{{request()->input('dtainicio')}}" autocomplete="off">
              </div>
              <div class="form-group col-md-4">
                <label for="dtafinal">Data final</label>
                <input type="text" class="form-control" id="dtafinal" name="dtafinal" value="{{request()->input('dtafinal')}}" autocomplete="off">
              </div>  
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="nome">Nome do Funcionário</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{request()->input('nome')}}">
              </div>
              <div class="form-group col-md-4">
                <label for="setor">Setor do Funcionário</label>
                <input type="text" class="form-control" id="setor" name="setor" value="{{request()->input('setor')}}">
              </div>
              <div class="form-group col-md-4">
                <label for="operador">Nome do Operador</label>
                <input type="text" class="form-control" id="operador" name="operador" value="{{request()->input('operador')}}">
              </div>  
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="protocolo_tipo_id">Tipo do Protocolo</label>
                <select class="form-control" name="protocolo_tipo_id" id="protocolo_tipo_id">
                  <option value="">Mostrar todos</option>    
                  @foreach($protocolotipos as $protocolotipo)
                  <option value="{{$protocolotipo->id}}" {{ ($protocolotipo->id == request()->input('protocolo_tipo_id')) ? ' selected' : '' }} >{{$protocolotipo->descricao}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="protocolo_situacao_id">Situação do Protocolo</label>
                <select class="form-control" name="protocolo_situacao_id" id="protocolo_situacao_id">
                  <option value="">Mostrar todos</option>
                  @foreach($protocolosituacoes as $protocolosituacao)
                  <option value="{{$protocolosituacao->id}}" {{ ($protocolosituacao->id == request()->input('protocolo_situacao_id')) ? ' selected' : '' }} >{{$protocolosituacao->descricao}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="protocolo_grupo_id">Grupo de Trabalho</label>
                <select class="form-control" name="protocolo_grupo_id" id="protocolo_grupo_id">
                  <option value="">Mostrar todos</option>
                  @foreach($protocologrupos as $protocologrupo)
                  <option value="{{$protocologrupo->id}}" {{ ($protocologrupo->id == request()->input('protocolo_grupo_id')) ? ' selected' : '' }} >{{$protocologrupo->descricao}}</option>
                  @endforeach
                </select>
              </div>  
            </div> 
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Pesquisar</button>
            <a href="{{ route('protocolos.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
          </form>
          <br>
          <!-- Seleção de número de resultados por página -->
          <div class="form-group">
            <select class="form-control" name="perpage" id="perpage">
              @foreach($perpages as $perpage)
              <option value="{{$perpage->valor}}"  {{($perpage->valor == session('perPage')) ? 'selected' : ''}}>{{$perpage->nome}}</option>
              @endforeach
            </select>
          </div>
        </div>     
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalEncaminhar" tabindex="-1" role="dialog" aria-labelledby="JanelaConcluir" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-file-download"></i> Relatório de Encaminhamento Diário</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="dtaencaminhamento">Data de consulta</label>
                <input type="text" class="form-control" id="dtaencaminhamento" name="dtaencaminhamento" value="{{request()->input('dtaencaminhamento')}}" autocomplete="off">  
            </div>
            <div class="form-group col-md-6">
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio1" name="turnoencaminhamento" class="custom-control-input" value="1" checked>
                <label class="custom-control-label" for="customRadio1">Manhã</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio2" name="turnoencaminhamento" class="custom-control-input" value="2">
                <label class="custom-control-label" for="customRadio2">Tarde</label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-primary" id="botaoencaminhar"><i class="fas fa-file-download"></i> Imprimir</button>
          </div>
        </form>
      </div>     
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('script-footer')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('locales/bootstrap-datepicker.pt-BR.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('#perpage').on('change', function() {
        perpage = $(this).find(":selected").val(); 
        
        window.open("{{ route('protocolos.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_numprotocolo = $('input[name="numprotocolo"]').val();
        var filtro_nome = $('input[name="nome"]').val();
        var filtro_setor = $('input[name="setor"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_protocolo_tipo_id = $('select[name="protocolo_tipo_id"]').val();
        if (typeof filtro_protocolo_tipo_id === "undefined") {
          filtro_protocolo_tipo_id = "";
        }
        var filtro_protocolo_situacao_id = $('select[name="protocolo_situacao_id"]').val();
        if (typeof filtro_protocolo_situacao_id === "undefined") {
          filtro_protocolo_situacao_id = "";
        }
        var filtro_protocolo_grupo_id = $('select[name="protocolo_grupo_id"]').val();
        if (typeof filtro_protocolo_grupo_id === "undefined") {
          filtro_protocolo_grupo_id = "";
        }    
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();
        window.open("{{ route('protocolos.export.csv') }}" + "?numprotocolo=" + filtro_numprotocolo + "&nome=" + filtro_nome + "&setor=" + filtro_setor + "&operador=" + filtro_operador + "&protocolo_tipo_id=" + filtro_protocolo_tipo_id + "&protocolo_situacao_id=" + filtro_protocolo_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal + "&protocolo_grupo_id=" + filtro_protocolo_grupo_id, "_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_numprotocolo = $('input[name="numprotocolo"]').val();
        var filtro_nome = $('input[name="nome"]').val();
        var filtro_setor = $('input[name="setor"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_protocolo_tipo_id = $('select[name="protocolo_tipo_id"]').val();
        if (typeof filtro_protocolo_tipo_id === "undefined") {
          filtro_protocolo_tipo_id = "";
        }
        var filtro_protocolo_situacao_id = $('select[name="protocolo_situacao_id"]').val();
        if (typeof filtro_protocolo_situacao_id === "undefined") {
          filtro_protocolo_situacao_id = "";
        }
        var filtro_protocolo_grupo_id = $('select[name="protocolo_grupo_id"]').val();
        if (typeof filtro_protocolo_grupo_id === "undefined") {
          filtro_protocolo_grupo_id = "";
        }     
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();
        window.open("{{ route('protocolos.export.pdf') }}" + "?numprotocolo=" + filtro_numprotocolo + "&nome=" + filtro_nome + "&setor=" + filtro_setor + "&operador=" + filtro_operador + "&protocolo_tipo_id=" + filtro_protocolo_tipo_id + "&protocolo_situacao_id=" + filtro_protocolo_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal + "&protocolo_grupo_id=" + filtro_protocolo_grupo_id, "_self");
    });

    $('#btnExportarPDFporsetor').on('click', function(){
        var filtro_numprotocolo = $('input[name="numprotocolo"]').val();
        var filtro_nome = $('input[name="nome"]').val();
        var filtro_setor = $('input[name="setor"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_protocolo_tipo_id = $('select[name="protocolo_tipo_id"]').val();
        if (typeof filtro_protocolo_tipo_id === "undefined") {
          filtro_protocolo_tipo_id = "";
        }
        var filtro_protocolo_situacao_id = $('select[name="protocolo_situacao_id"]').val();
        if (typeof filtro_protocolo_situacao_id === "undefined") {
          filtro_protocolo_situacao_id = "";
        }
        var filtro_protocolo_grupo_id = $('select[name="protocolo_grupo_id"]').val();
        if (typeof filtro_protocolo_grupo_id === "undefined") {
          filtro_protocolo_grupo_id = "";
        }       
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();
        window.open("{{ route('protocolos.export.porsetor.pdf') }}" + "?numprotocolo=" + filtro_numprotocolo + "&nome=" + filtro_nome + "&setor=" + filtro_setor + "&operador=" + filtro_operador + "&protocolo_tipo_id=" + filtro_protocolo_tipo_id + "&protocolo_situacao_id=" + filtro_protocolo_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal + "&protocolo_grupo_id=" + filtro_protocolo_grupo_id, "_self");
    });

    $('#btnExportarPDFporsetorsimples').on('click', function(){
        var filtro_numprotocolo = $('input[name="numprotocolo"]').val();
        var filtro_nome = $('input[name="nome"]').val();
        var filtro_setor = $('input[name="setor"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_protocolo_tipo_id = $('select[name="protocolo_tipo_id"]').val();
        if (typeof filtro_protocolo_tipo_id === "undefined") {
          filtro_protocolo_tipo_id = "";
        }
        var filtro_protocolo_situacao_id = $('select[name="protocolo_situacao_id"]').val();
        if (typeof filtro_protocolo_situacao_id === "undefined") {
          filtro_protocolo_situacao_id = "";
        }
        var filtro_protocolo_grupo_id = $('select[name="protocolo_grupo_id"]').val();
        if (typeof filtro_protocolo_grupo_id === "undefined") {
          filtro_protocolo_grupo_id = "";
        }   
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();
        window.open("{{ route('protocolos.export.porsetor.simples.pdf') }}" + "?numprotocolo=" + filtro_numprotocolo + "&nome=" + filtro_nome + "&setor=" + filtro_setor + "&operador=" + filtro_operador + "&protocolo_tipo_id=" + filtro_protocolo_tipo_id + "&protocolo_situacao_id=" + filtro_protocolo_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal + "&protocolo_grupo_id=" + filtro_protocolo_grupo_id, "_self");
    });

    $('#botaoencaminhar').on('click', function(){

        var filtro_dta_encaminhamento = $('input[name="dtaencaminhamento"]').val();

        var filtro_turnoencaminhamento = $('input[name="turnoencaminhamento"]:checked').val();
        if (typeof filtro_turnoencaminhamento === "undefined") {
          filtro_turnoencaminhamento = "";
        }

        window.open("{{ route('protocolos.export.encaminhamento.pdf') }}" + "?dtaencaminhamento=" + filtro_dta_encaminhamento + "&turnoencaminhamento=" + filtro_turnoencaminhamento, "_self");
    });

    $('#dtainicio').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        clearBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true
    });

    $('#dtafinal').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        clearBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true
    });

    $('#dtaencaminhamento').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        clearBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true
    });
}); 
</script>
@endsection