@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('memorandotipos.index') }}">Lista de Tipos de Memorando</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">

  <div class="card">
    <div class="card-header">
      Tipo de Memorando
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Descrição: {{$memorandotipos->descricao}}</li>
      </ul>
    </div>
    <div class="card-footer text-muted">
      <form method="post" action="{{route('memorandotipos.destroy', $memorandotipos->id)}}">
        @csrf
        @method('DELETE')
        <a href="{{ route('memorandotipos.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>  
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
      </form>
    </div>
  </div>  
  <br>
</div>

@endsection