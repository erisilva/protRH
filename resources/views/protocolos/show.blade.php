@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('protocolos.index') }}">Lista de Funcionários</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">

  <div class="card">
    <div class="card-header">
      Protocolo
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item"><b>Número: {{$protocolo->id}}</b></li>
        <li class="list-group-item">Data/Hora: {{$protocolo->created_at->format('d/m/Y H:i')}}</li>
        <li class="list-group-item">Funcionário: {{$protocolo->funcionario->nome}}</li>
        <li class="list-group-item">Setor: {{$protocolo->setor->descricao}}</li>
        <li class="list-group-item">Tipo: {{$protocolo->protocoloTipo->descricao}}</li>
        <li class="list-group-item">Situação: {{$protocolo->protocoloSituacao->descricao}}</li>
        <li class="list-group-item">Observações: {{$protocolo->descricao}}</li>
        <li class="list-group-item">Operador: {{$protocolo->user->name}}</li>
      </ul>
    </div>
    <div class="card-footer text-muted">
      <form method="post" action="{{route('protocolos.destroy', $protocolo->id)}}">
        @csrf
        @method('DELETE')
        <a href="{{ route('protocolos.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>  
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
      </form>
    </div>
  </div>  
  <br>
</div>

@endsection
