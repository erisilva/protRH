@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('solicitacaosituacoes.index') }}">Lista de Situações da Solicitação</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  {{-- avisa se uma permissão foi alterada --}}
  @if(Session::has('edited_solicitacaosituacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_solicitacaosituacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <form method="POST" action="{{ route('solicitacaosituacoes.update', $solicitacaosituacao->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="descricao">Descrição</label>
        <input type="text" class="form-control{{ $errors->has('descricao') ? ' is-invalid' : '' }}" name="descricao" value="{{ old('descricao') ?? $solicitacaosituacao->descricao }}">
        @if ($errors->has('descricao'))
        <div class="invalid-feedback">
        {{ $errors->first('descricao') }}
        </div>
        @endif
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar Dados do Situação da Solicitação</button>
  </form>
</div>
<div class="container">
  <div class="float-right">
    <a href="{{ route('solicitacaosituacoes.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
