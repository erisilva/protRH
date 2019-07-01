@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('solicitacoes.index') }}">Lista de Solicitações</a></li>
      <li class="breadcrumb-item active" aria-current="page">Novo Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form method="POST" action="{{ route('solicitacoes.store') }}">
    @csrf
    <div class="form-group">
      <label for="remetente">Remetente(s)</label>
      <textarea class="form-control{{ $errors->has('remetente') ? ' is-invalid' : '' }}" name="remetente" rows="3">{{ old('remetente') ?? '' }}</textarea>
      @if ($errors->has('remetente'))
      <div class="invalid-feedback">
      {{ $errors->first('remetente') }}
      </div>
      @endif     
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="identificacao">Identificação</label>
        <input type="text" class="form-control" id="identificacao" name="identificacao" value="{{{ old('identificacao') ?? '' }}}">
      </div>
      <div class="form-group col-md-4">
        <label for="solicitacao_tipo_id">Tipo de Solicitação</label>
        <select class="form-control {{ $errors->has('solicitacao_tipo_id') ? ' is-invalid' : '' }}" name="solicitacao_tipo_id" id="solicitacao_tipo_id">
          <option value="" selected="true">Selecione ...</option>        
          @foreach($solicitacaotipos as $solicitacaotipo)
          <option value="{{$solicitacaotipo->id}}">{{$solicitacaotipo->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('solicitacao_tipo_id'))
        <div class="invalid-feedback">
        {{ $errors->first('solicitacao_tipo_id') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-4">
        <label for="solicitacao_situacao_id">Situação de Solicitação</label>
        <select class="form-control {{ $errors->has('solicitacao_situacao_id') ? ' is-invalid' : '' }}" name="solicitacao_situacao_id" id="solicitacao_situacao_id">
          <option value="" selected="true">Selecione ...</option>        
          @foreach($solicitacaosituacoes as $solicitacaosituacoe)
          <option value="{{$solicitacaosituacoe->id}}">{{$solicitacaosituacoe->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('solicitacao_situacao_id'))
        <div class="invalid-feedback">
        {{ $errors->first('solicitacao_situacao_id') }}
        </div>
        @endif
      </div>
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3">{{ old('observacao') ?? '' }}</textarea>      
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Solicitação</button>
  </form>
  <div class="float-right">
    <a href="{{ route('solicitacoes.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
