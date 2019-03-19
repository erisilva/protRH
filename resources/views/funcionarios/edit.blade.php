@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Funcionários</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  {{-- avisa se uma permissão foi alterada --}}
  @if(Session::has('edited_funcionario'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_funcionario') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <form method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="nome">Nome</label>
        <input type="text" class="form-control{{ $errors->has('nome') ? ' is-invalid' : '' }}" name="nome" value="{{ old('nome') ?? $funcionario->nome }}">
        @if ($errors->has('nome'))
        <div class="invalid-feedback">
        {{ $errors->first('nome') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-4">
        <label for="matricula">Matrícula</label>
        <input type="text" class="form-control{{ $errors->has('matricula') ? ' is-invalid' : '' }}" name="matricula" value="{{ old('matricula') ?? $funcionario->matricula }}">
        @if ($errors->has('matricula'))
        <div class="invalid-feedback">
        {{ $errors->first('matricula') }}
        </div>
        @endif
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-10">
        <label for="email">E-mail</label>
        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') ?? $funcionario->email }}">
        @if ($errors->has('email'))
        <div class="invalid-feedback">
        {{ $errors->first('email') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-2">
        <label for="numeropasta">Nº Pasta</label>
        <input type="text" class="form-control{{ $errors->has('numeropasta') ? ' is-invalid' : '' }}" name="numeropasta" value="{{ old('numeropasta') ?? $funcionario->numeropasta }}">
        @if ($errors->has('numeropasta'))
        <div class="invalid-feedback">
        {{ $errors->first('numeropasta') }}
        </div>
        @endif
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar Dados do Funcionário</button>
  </form>
</div>
<div class="container">
  <div class="float-right">
    <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
