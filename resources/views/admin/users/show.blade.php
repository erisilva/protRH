@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">

  <div class="card">
    <div class="card-header">
      Operador
    </div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">Nome: {{$user->name}}</li>
        <li class="list-group-item">E-mail: {{$user->email}}</li>
        <li class="list-group-item">Ativo: {{($user->active == 'Y') ? 'Sim' : 'Não'}} </li>
      </ul>
    </div>
    <div class="card-footer text-muted">
      <form method="post" action="{{route('users.destroy', $user->id)}}">
        @csrf
        @method('DELETE')
        <a href="{{ route('users.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>  
        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
      </form>
    </div>
  </div>  
  <br>
</div>

@endsection
