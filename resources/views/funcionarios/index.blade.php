@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('funcionarios.index') }}">Lista de Funcionários</a></li>
    </ol>
  </nav>
  {{-- avisa se um usuario foi excluido --}}
  @if(Session::has('deleted_funcionario'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('deleted_funcionario') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  {{-- avisa quando um usuário foi modificado --}}
  @if(Session::has('create_funcionario'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_funcionario') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="btn-group py-1" role="group" aria-label="Opções">
    <a href="{{ route('funcionarios.create') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-plus-square"></i> Novo Registro</a>
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
                <th scope="col">Nome</th>
                <th scope="col">Matrícula</th>
                <th scope="col">E-mail</th>
                <th scope="col">Nº Pasta</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($funcionarios as $funcionario)
            <tr>
                <td>{{$funcionario->nome}}</td>
                <td>{{$funcionario->matricula}}</td>
                <td>{{$funcionario->email}}</td>
                <td>{{$funcionario->numeropasta}}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{route('funcionarios.edit', $funcionario->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-edit"></i></a>
                    <a href="{{route('funcionarios.show', $funcionario->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fas fa-trash-alt"></i></a>
                  </div>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div>
  <p class="text-center">Página {{ $funcionarios->currentPage() }} de {{ $funcionarios->lastPage() }}. Total de registros: {{ $funcionarios->total() }}.</p>
  <div class="container-fluid">
      {{ $funcionarios->links() }}
  </div>
  <!-- Janela de filtragem da consulta -->
  <div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="JanelaFiltro" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-filter"></i> Filtro</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Filtragem dos dados -->
          <form method="GET" action="{{ route('funcionarios.index') }}">
            <div class="form-group">
              <label for="nome">Nome</label>
              <input type="text" class="form-control" id="nome" name="nome" value="{{request()->input('nome')}}">
            </div>
            <div class="form-group">
              <label for="matricula">Matricula</label>
              <input type="text" class="form-control" id="matricula" name="matricula" value="{{request()->input('matricula')}}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Pesquisar</button>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-primary btn-sm" role="button">Limpar</a>
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
<script>
$(document).ready(function(){
    $('#perpage').on('change', function() {
        perpage = $(this).find(":selected").val(); 
        
        window.open("{{ route('funcionarios.index') }}" + "?perpage=" + perpage,"_self");
    });

    $('#btnExportarCSV').on('click', function(){
        var filtro_name = $('input[name="nome"]').val();
        var filtro_email = $('input[name="matricula"]').val();
        window.open("{{ route('funcionarios.export.csv') }}" + "?nome=" + filtro_name + "&matricula=" + filtro_email,"_self");
    });

    $('#btnExportarPDF').on('click', function(){
        var filtro_name = $('input[name="nome"]').val();
        var filtro_email = $('input[name="matricula"]').val();
        window.open("{{ route('funcionarios.export.pdf') }}" + "?nome=" + filtro_name + "&matricula=" + filtro_email,"_self");
    });
}); 
</script>
@endsection