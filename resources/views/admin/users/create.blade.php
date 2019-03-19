@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item active" aria-current="page">Novo Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') ?? '' }}">
        @if ($errors->has('name'))
        <div class="invalid-feedback">
        {{ $errors->first('name') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-6">
        <label for="email">E-mail</label>
        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') ?? '' }}">
        @if ($errors->has('email'))
        <div class="invalid-feedback">
        {{ $errors->first('email') }}
        </div>
        @endif
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="password">Senha</label>
        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
        @if ($errors->has('password'))
        <div class="invalid-feedback">
        {{ $errors->first('password') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-6">
        <label for="password_confirmation">Confirme a senha</label>
        <input type="password" class="form-control" name="password_confirmation">
      </div>
    </div>
    <div class="container bg-primary text-white">
      <p class="text-center">Perfis</p>
    </div>
    <div class="form-row">
      @foreach($roles as $role)
        @php
          $checked = '';
          if(old('roles') ?? false){
            foreach (old('roles') as $key => $id) {
              if($id == $role->id){
                $checked = "checked";
              }
            }
          }
        @endphp
      <div class="form-group col-4">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="roles[]" value="{{$role->id}}" {{$checked}}>
            <label class="form-check-label" for="roles">{{$role->description}}</label>
        </div>
      </div>
      @endforeach
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Operador</button>
  </form>
</div>
<div class="container">
  <div class="float-right">
    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
