@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('memorandos.index') }}">Lista de Memorandos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Novo Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form method="POST" action="{{ route('memorandos.store') }}">
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
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="memorando_tipo_id">Tipo do Memorando</label>
        <select class="form-control {{ $errors->has('memorando_tipo_id') ? ' is-invalid' : '' }}" name="memorando_tipo_id" id="memorando_tipo_id">
          <option value="" selected="true">Selecione ...</option>        
          @foreach($memorandotipos as $memorandotipo)
          <option value="{{$memorandotipo->id}}">{{$memorandotipo->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('memorando_tipo_id'))
        <div class="invalid-feedback">
        {{ $errors->first('memorando_tipo_id') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-6">
        <label for="memorando_situacao_id">Situação do Memorando</label>
        <select class="form-control {{ $errors->has('memorando_situacao_id') ? ' is-invalid' : '' }}" name="memorando_situacao_id" id="memorando_situacao_id">
          <option value="" selected="true">Selecione ...</option>        
          @foreach($memorandosituacoes as $memorandosituacao)
          <option value="{{$memorandosituacao->id}}">{{$memorandosituacao->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('memorando_situacao_id'))
        <div class="invalid-feedback">
        {{ $errors->first('memorando_situacao_id') }}
        </div>
        @endif
      </div>
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3">{{ old('observacao') ?? '' }}</textarea>      
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Memorando</button>
  </form>
  <div class="float-right">
    <a href="{{ route('memorandos.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
