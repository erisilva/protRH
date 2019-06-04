@extends('layouts.app')

@section('css-header')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('memorandos.index') }}">Lista de Memorandos</a></li>
    </ol>
  </nav>
  {{-- avisa se um usuario foi excluido --}}
  @if(Session::has('deleted_memorando'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('deleted_memorando') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  {{-- avisa quando um usuário foi modificado --}}
  @if(Session::has('create_memorando'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_memorando') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route('memorandos.create') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-plus-square"></i> Novo Registro</a>
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
                <th scope="col">Tipo</th>
                <th scope="col">Situação</th>
                <th scope="col">Operador</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($memorandos as $memorando)
            <tr>
                <td><strong>{{$memorando->id}}</strong></td>
                <td>{{$memorando->created_at->format('d/m/Y')}}</td>
                <td>{{$memorando->created_at->format('H:i')}}</td>
                <td>{{$memorando->remetente}}</td>
                <td>{{$memorando->memorandoTipo->descricao}}</td>
                <td>{{$memorando->memorandoSituacao->descricao}}</td>
                <td>{{$memorando->user->name}}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('memorandos.edit', $memorando->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-edit"></i></a>
                    <a href="{{route('memorandos.show', $memorando->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('memorandos.export.pdf.individual', $memorando->id) }}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-print"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $memorandos->currentPage() }} de {{ $memorandos->lastPage() }}. Total de registros: {{ $memorandos->total() }}.</p>
  <div class="container-fluid">
      {{ $memorandos->links() }}
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
          <form method="GET" action="{{ route('memorandos.index') }}">
            <div class="form-group">
              <label for="remetente">Remetente</label>
                <input type="text" class="form-control" id="remetente" name="remetente" value="{{request()->input('remetente')}}">
            </div>  
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="numeromemorando">Nº(RH)</label>
                <input type="text" class="form-control" id="numeromemorando" name="numeromemorando" value="{{request()->input('numeromemorando')}}">
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
                <label for="numero">Número</label>
                <input type="text" class="form-control" id="numero" name="numero" value="{{request()->input('numero')}}">
              </div>
              <div class="form-group col-md-4">
                <label for="memorando_tipo_id">Tipo do Memorando</label>
                <select class="form-control" name="memorando_tipo_id" id="memorando_tipo_id">
                  <option value="">Mostrar todos</option>    
                  @foreach($memorandotipos as $memorandotipo)
                  <option value="{{$memorandotipo->id}}" {{ ($memorandotipo->id == request()->input('memorando_tipo_id')) ? ' selected' : '' }} >{{$memorandotipo->descricao}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-md-4">
                <label for="memorando_situacao_id">Situação do Memorando</label>
                <select class="form-control" name="memorando_situacao_id" id="memorando_situacao_id">
                  <option value="">Mostrar todos</option>
                  @foreach($memorandosituacoes as $memorandosituacao)
                  <option value="{{$memorandosituacao->id}}" {{ ($memorandosituacao->id == request()->input('memorando_situacao_id')) ? ' selected' : '' }} >{{$memorandosituacao->descricao}}</option>
                  @endforeach
                </select>
              </div>  
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Pesquisar</button>
            <a href="{{ route('memorandos.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
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
        
        window.open("{{ route('memorandos.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numeromemorando = $('input[name="numeromemorando"]').val();
        var filtro_memorando_tipo_id = $('select[name="memorando_tipo_id"]').val();
        if (typeof filtro_memorando_tipo_id === "undefined") {
          filtro_memorando_tipo_id = "";
        }
        var filtro_memorando_situacao_id = $('select[name="memorando_situacao_id"]').val();
        if (typeof filtro_memorando_situacao_id === "undefined") {
          filtro_memorando_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('memorandos.export.csv') }}" + "?remetente=" + filtro_remetente + "&numeromemorando=" + filtro_numeromemorando + "&memorando_tipo_id=" + filtro_memorando_tipo_id + "&memorando_situacao_id=" + filtro_memorando_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_remetente = $('input[name="remetente"]').val();
        var filtro_numeromemorando = $('input[name="numeromemorando"]').val();
        var filtro_memorando_tipo_id = $('select[name="memorando_tipo_id"]').val();
        if (typeof filtro_memorando_tipo_id === "undefined") {
          filtro_memorando_tipo_id = "";
        }
        var filtro_memorando_situacao_id = $('select[name="memorando_situacao_id"]').val();
        if (typeof filtro_memorando_situacao_id === "undefined") {
          filtro_memorando_situacao_id = "";
        }        
        var filtro_dtainicio = $('input[name="dtainicio"]').val();
        var filtro_dtafinal = $('input[name="dtafinal"]').val();

        window.open("{{ route('memorandos.export.pdf') }}" + "?remetente=" + filtro_remetente + "&numeromemorando=" + filtro_numeromemorando + "&numero=" + filtro_memorando_tipo_id + "&memorando_situacao_id=" + filtro_memorando_situacao_id + "&dtainicio=" + filtro_dtainicio + "&dtafinal=" + filtro_dtafinal,"_self");
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