@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('protocolos.index') }}">Lista de Protocolos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  {{-- avisa se uma permissão foi alterada --}}
  @if(Session::has('edited_protocolo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_protocolo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
    {{-- avisa quando um usuário foi criado --}}
  @if(Session::has('create_protocolo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_protocolo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <form method="POST" action="{{ route('protocolos.update', $protocolo->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-2">
        <label for="funcionario">Nº</label>
        <input type="text" class="form-control" name="funcionario" value="{{ $protocolo->id }}" readonly>
      </div>
      <div class="form-group col-md-5">
        <label for="funcionario">Funcionario</label>
        <input type="text" class="form-control" name="funcionario" value="{{ $protocolo->funcionario->nome }}" readonly>
        <input type="hidden" id="funcionario_id" name="funcionario_id" value="{{ $protocolo->funcionario_id }}">
      </div>
      <div class="form-group col-md-5">
        <label for="setor">Setor</label>
        <input type="text" class="form-control" name="setor" value="{{ $protocolo->setor->descricao }}" readonly>
        <input type="hidden" id="setor_id" name="setor_id" value="{{ $protocolo->setor_id }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="protocolo_tipo_id">Tipo do Protocolo</label>
        <select class="form-control" name="protocolo_tipo_id" id="protocolo_tipo_id">
          <option value="{{ $protocolo->protocolo_tipo_id }}" selected="true"> &rarr; {{ $protocolo->protocoloTipo->descricao }}</option>        
          @foreach($protocolotipos as $protocolotipo)
          <option value="{{$protocolotipo->id}}">{{$protocolotipo->descricao}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-4">
        <label for="protocolo_situacao_id">Situação do Protocolo</label>
        <select class="form-control" name="protocolo_situacao_id" id="protocolo_situacao_id">
          <option value="{{$protocolo->protocolo_situacao_id}}" selected="true"> &rarr; {{ $protocolo->protocoloSituacao->descricao }}</option>
          @foreach($protocolosituacoes as $protocolosituacao)
          <option value="{{$protocolosituacao->id}}">{{$protocolosituacao->descricao}}</option>
          @endforeach
        </select>
      </div>      
    </div>
    <div class="form-group">
      <label for="descricao">Observações</label>
      <textarea class="form-control" name="descricao" rows="3">{{ $protocolo->descricao }}</textarea>      
    </div>

    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar Dados do Protocolo</button>
  </form>
</div>
<br>
<div class="container bg-primary text-white">
  <p class="text-center">Períodos</p>
</div>
<div class="container">
  <div class="row">
    <div class="col-md-4">
      <form>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="dtainicio">Data inicial</label>
            <input type="text" class="form-control" id="dtainicio" name="dtainicio" value="" autocomplete="off">
          </div>
          <div class="form-group col-md-6">
            <label for="dtafinal">Data final</label>
            <input type="text" class="form-control" id="dtafinal" name="dtafinal" value="" autocomplete="off"> 
          </div>  
        </div>
        <div class="form-group">
          <label for="periodo_tipo_id">Situação do Protocolo</label>
          <select class="form-control" name="periodo_tipo_id" id="periodo_tipo_id">
            <option value="" selected="true">Selecionar ... </option>
            @foreach($periodotipos as $periodotipo)
            <option value="{{$periodotipo->id}}">{{$periodotipo->descricao}}</option>
            @endforeach
          </select>          
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Incluir Período</button>    
      </form>  
    </div>
    <div class="col-md-8">
  
    </div>    
  </div>
</div>
<div class="container">
  <div class="float-right">
    <a href="{{ route('protocolos.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
