@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('memorandos.index') }}">Lista de Memorandos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form>
    <div class="form-row">
      <div class="form-group col-md-3">
        <div class="p-3 bg-primary text-white text-right h2">Nº {{ $memorando->id }}</div>    
      </div>
      <div class="form-group col-md-2">
        <label for="dia">Data</label>
        <input type="text" class="form-control" name="dia" value="{{ $memorando->created_at->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora">Hora</label>
        <input type="text" class="form-control" name="hora" value="{{ $memorando->created_at->format('H:i') }}" readonly>
      </div>
      <div class="form-group col-md-5">
        <label for="setor">Operador</label>
        <input type="text" class="form-control" name="setor" value="{{ $memorando->user->name }}" readonly>
      </div>
    </div>
    <div class="form-group">
      <label for="remetente">Remetente</label>
      <textarea class="form-control" name="remetente" rows="3" readonly>{{ $memorando->remetente }}</textarea>      
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="memorando_tipo">Tipo do Memorando</label>
        <input type="text" class="form-control" name="memorando_tipo" value="{{ $memorando->memorandoTipo->descricao }}" readonly>
      </div>
      <div class="form-group col-md-6">
        <label for="memorando_situacao">Situação do Memorando</label>
        <input type="text" class="form-control" name="memorando_situacao" value="{{ $memorando->memorandoSituacao->descricao }}" readonly>
      </div>      
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3" readonly>{{ $memorando->observacao }}</textarea>      
    </div>
  </form>
  <br>
  <div class="container">
    <form method="post" action="{{route('memorandos.destroy', $memorando->id)}}">
      @csrf
      @method('DELETE')
      <a href="{{ route('memorandos.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
      <a href="{{ route('memorandos.export.pdf.individual', $memorando->id) }}" class="btn btn-primary" role="button"><i class="fas fa-print"></i> Exportar para PDF</a>
      <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
    </form>
  </div>
</div>

@endsection
