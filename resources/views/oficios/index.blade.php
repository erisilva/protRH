@extends('layouts.app')

@section('css-header')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('oficios.index') }}">Lista de Ofícios</a></li>
    </ol>
  </nav>
  {{-- avisa se um usuario foi excluido --}}
  @if(Session::has('deleted_oficio'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('deleted_oficio') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  {{-- avisa quando um usuário foi modificado --}}
  @if(Session::has('create_oficio'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_oficio') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route('oficios.create') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-plus-square"></i> Novo Registro</a>
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
                <th scope="col">Remetente(s)/Assunto</th>
                <th scope="col">Tipo</th>
                <th scope="col">Situação</th>
                <th scope="col">Operador</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($oficios as $oficio)
            <tr>
                <td><strong>{{$oficio->id}}</strong></td>
                <td>{{$oficio->created_at->format('d/m/Y')}}</td>
                <td>{{$oficio->created_at->format('H:i')}}</td>
                <td>{{$oficio->remetente}}</td>
                <td>{{$oficio->oficioTipo->descricao}}</td>
                <td>{{$oficio->oficioSituacao->descricao}}</td>
                <td>{{$oficio->user->name}}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('oficios.edit', $oficio->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-edit"></i></a>
                    <a href="{{route('oficios.show', $oficio->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('oficios.export.pdf.individual', $oficio->id) }}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-print"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $oficios->currentPage() }} de {{ $oficios->lastPage() }}. Total de registros: {{ $oficios->total() }}.</p>
  <div class="container-fluid">
      {{ $oficios->links() }}
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
          <form method="GET" action="{{ route('oficios.index') }}">
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
                <label for="oficio_tipo_id">Tipo do Ofício</label>
                <select class="form-control" name="oficio_tipo_id" id="oficio_tipo_id">
                  <option value="">Mostrar todos</option>    
                  @foreach($oficiotipos as $oficiotipo)
                  <option value="{{$oficiotipo->id}}" {{ ($oficiotipo->id == request()->input('oficio_tipo_id')) ? ' selected' : '' }} >{{$oficiotipo->descricao}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="oficio_situacao_id">Situação do Ofício</label>
                <select class="form-control" name="oficio_situacao_id" id="oficio_situacao_id">
                  <option value="">Mostrar todos</option>
                  @foreach($oficiosituacoes as $oficiosituacao)
                  <option value="{{$oficiosituacao->id}}" {{ ($oficiosituacao->id == request()->input('oficio_situacao_id')) ? ' selected' : '' }} >{{$oficiosituacao->descricao}}</option>
                  @endforeach
                </select>
              </div>  
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Pesquisar</button>
            <a href="{{ route('oficios.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
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
        
        window.open("{{ route('oficios.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numero = $('input[name="numero"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_oficio_tipo_id = $('select[name="oficio_tipo_id"]').val();
        if (typeof filtro_oficio_tipo_id === "undefined") {
          filtro_oficio_tipo_id = "";
        }
        var filtro_oficio_situacao_id = $('select[name="oficio_situacao_id"]').val();
        if (typeof filtro_oficio_situacao_id === "undefined") {
          filtro_oficio_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('oficios.export.csv') }}" + "?remetente=" + filtro_remetente + "&numero=" + filtro_numero + "&operador=" + filtro_operador + "&oficio_tipo_id=" + filtro_oficio_tipo_id + "&oficio_situacao_id=" + filtro_oficio_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numero = $('input[name="numero"]').val();
        var filtro_operador = $('input[name="operador"]').val();
        var filtro_oficio_tipo_id = $('select[name="oficio_tipo_id"]').val();
        if (typeof filtro_oficio_tipo_id === "undefined") {
          filtro_oficio_tipo_id = "";
        }
        var filtro_oficio_situacao_id = $('select[name="oficio_situacao_id"]').val();
        if (typeof filtro_oficio_situacao_id === "undefined") {
          filtro_oficio_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('oficios.export.pdf') }}" + "?remetente=" + filtro_remetente + "&numero=" + filtro_numero + "&operador=" + filtro_operador + "&oficio_tipo_id=" + filtro_oficio_tipo_id + "&oficio_situacao_id=" + filtro_oficio_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
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