@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('oficios.index') }}">Lista de Ofícios</a></li>
      <li class="breadcrumb-item active" aria-current="page">Novo Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form method="POST" action="{{ route('oficios.store') }}">
    @csrf
    <div class="form-group">
      <label for="remetente">Remetente(s)/Assunto</label>
      <textarea class="form-control{{ $errors->has('remetente') ? ' is-invalid' : '' }}" name="remetente" rows="3">{{ old('remetente') ?? '' }}</textarea>
      @if ($errors->has('remetente'))
      <div class="invalid-feedback">
      {{ $errors->first('remetente') }}
      </div>
      @endif     
    </div>
    <div class="form-group">
      <label for="oficio_tipo_id">Tipo de Ofício</label>
      <select class="form-control {{ $errors->has('oficio_tipo_id') ? ' is-invalid' : '' }}" name="oficio_tipo_id" id="oficio_tipo_id">
        <option value="" selected="true">Selecione ...</option>        
        @foreach($oficiotipos as $oficiotipo)
        <option value="{{$oficiotipo->id}}">{{$oficiotipo->descricao}}</option>
        @endforeach
      </select>
      @if ($errors->has('oficio_tipo_id'))
      <div class="invalid-feedback">
      {{ $errors->first('oficio_tipo_id') }}
      </div>
      @endif
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3">{{ old('observacao') ?? '' }}</textarea>      
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Ofício</button>
  </form>
  <div class="float-right">
    <a href="{{ route('oficios.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
