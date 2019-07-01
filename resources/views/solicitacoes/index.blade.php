@extends('layouts.app')

@section('css-header')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('solicitacoes.index') }}">Lista de Solicitações</a></li>
    </ol>
  </nav>
  {{-- avisa se um usuario foi excluido --}}
  @if(Session::has('deleted_solicitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('deleted_solicitacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  {{-- avisa quando um usuário foi modificado --}}
  @if(Session::has('create_solicitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_solicitacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route('solicitacoes.create') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-plus-square"></i> Novo Registro</a>
    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter"></i> Filtrar</button>
    <div class="btn-group" role="group">
      <button id="btnGroupDropOptions" type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-print"></i> Relatórios
      </button>
      <div class="dropdown-menu" aria-labelledby="btnGroupDropOptions">
        <a class="dropdown-item" href="#" id="btnExportarCSV"><i class="fas fa-file-download"></i> Exportar Planilha</a>
        <a class="dropdown-item" href="#" id="btnExportarPDF"><i class="fas fa-file-download"></i> Exportar PDF</a>
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
                <th scope="col">Remetente</th>
                <th scope="col">Identificação</th>
                <th scope="col">Tipo</th>
                <th scope="col">Situação</th>
                <th scope="col">Operador</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($solicitacoes as $solicitacao)
            <tr>
                <td><strong>{{$solicitacao->id}}</strong></td>
                <td>{{$solicitacao->created_at->format('d/m/Y')}}</td>
                <td>{{$solicitacao->created_at->format('H:i')}}</td>
                <td>{{$solicitacao->remetente}}</td>
                <td>{{$solicitacao->identificacao}}</td>
                <td>{{$solicitacao->solicitacaoTipo->descricao}}</td>
                <td>{{$solicitacao->solicitacaoSituacao->descricao}}</td>
                <td>{{$solicitacao->user->name}}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('solicitacoes.edit', $solicitacao->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-edit"></i></a>
                    <a href="{{route('solicitacoes.show', $solicitacao->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('solicitacoes.export.pdf.individual', $solicitacao->id) }}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-print"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $solicitacoes->currentPage() }} de {{ $solicitacoes->lastPage() }}. Total de registros: {{ $solicitacoes->total() }}.</p>
  <div class="container-fluid">
      {{ $solicitacoes->links() }}
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
          <form method="GET" action="{{ route('solicitacoes.index') }}">
            <div class="form-group">
              <label for="remetente">Remetente</label>
                <input type="text" class="form-control" id="remetente" name="remetente" value="{{request()->input('remetente')}}">
            </div>  
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="numero">Nº</label>
                <input type="text" class="form-control" id="numero" name="numero" value="{{request()->input('numero')}}">
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
                <label for="operador">Operador</label>
                <input type="text" class="form-control" id="operador" name="operador" value="{{request()->input('operador')}}">
              </div>
              <div class="form-group col-md-4">
                <label for="solicitacao_tipo_id">Tipo da Solicitação</label>
                <select class="form-control" name="solicitacao_tipo_id" id="solicitacao_tipo_id">
                  <option value="">Mostrar todos</option>    
                  @foreach($solicitacaotipos as $solicitacaotipo)
                  <option value="{{$solicitacaotipo->id}}" {{ ($solicitacaotipo->id == request()->input('solicitacao_tipo_id')) ? ' selected' : '' }} >{{$solicitacaotipo->descricao}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="solicitacao_situacao_id">Situação da Solicitação</label>
                <select class="form-control" name="solicitacao_situacao_id" id="solicitacao_situacao_id">
                  <option value="">Mostrar todos</option>
                  @foreach($solicitacaosituacoes as $solicitacaosituacao)
                  <option value="{{$solicitacaosituacao->id}}" {{ ($solicitacaosituacao->id == request()->input('solicitacao_situacao_id')) ? ' selected' : '' }} >{{$solicitacaosituacao->descricao}}</option>
                  @endforeach
                </select>
              </div>  
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Pesquisar</button>
            <a href="{{ route('solicitacoes.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
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
@endsection
@section('script-footer')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('locales/bootstrap-datepicker.pt-BR.min.js') }}"></script>

<script>
$(document).ready(function(){
    $('#perpage').on('change', function() {
        perpage = $(this).find(":selected").val(); 
        
        window.open("{{ route('solicitacoes.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numero = $('input[name="numero"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_solicitacao_tipo_id = $('select[name="solicitacao_tipo_id"]').val();
        if (typeof filtro_solicitacao_tipo_id === "undefined") {
          filtro_solicitacao_tipo_id = "";
        }
        var filtro_solicitacao_situacao_id = $('select[name="solicitacao_situacao_id"]').val();
        if (typeof filtro_solicitacao_situacao_id === "undefined") {
          filtro_solicitacao_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('solicitacoes.export.csv') }}" + "?remetente=" + filtro_remetente + "&numero=" + filtro_numero + "&operador=" + filtro_operador + "&solicitacao_tipo_id=" + filtro_solicitacao_tipo_id + "&solicitacao_situacao_id=" + filtro_solicitacao_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numero = $('input[name="numero"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_solicitacao_tipo_id = $('select[name="solicitacao_tipo_id"]').val();
        if (typeof filtro_solicitacao_tipo_id === "undefined") {
          filtro_solicitacao_tipo_id = "";
        }
        var filtro_solicitacao_situacao_id = $('select[name="solicitacao_situacao_id"]').val();
        if (typeof filtro_solicitacao_situacao_id === "undefined") {
          filtro_solicitacao_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('solicitacoes.export.pdf') }}" + "?remetente=" + filtro_remetente + "&numero=" + filtro_numero + "&operador=" + filtro_operador + "&solicitacao_tipo_id=" + filtro_solicitacao_tipo_id + "&solicitacao_situacao_id=" + filtro_solicitacao_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
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
}); 
</script>
@endsection