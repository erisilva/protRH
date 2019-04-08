@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('protocolos.index') }}">Lista de Funcionários</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form>
    <div class="form-row">
      <div class="form-group col-md-3">
        <div class="p-3 bg-primary text-white text-right h2">Nº {{ $protocolo->id }}</div>    
      </div>
      <div class="form-group col-md-2">
        <label for="dia">Data</label>
        <input type="text" class="form-control" name="dia" value="{{ $protocolo->created_at->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora">Hora</label>
        <input type="text" class="form-control" name="hora" value="{{ $protocolo->created_at->format('H:i') }}" readonly>
      </div>
      <div class="form-group col-md-5">
        <label for="setor">Operador</label>
        <input type="text" class="form-control" name="setor" value="{{ $protocolo->user->name }}" readonly>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="funcionario">Funcionario</label>
        <input type="text" class="form-control" name="funcionario" value="{{ $protocolo->funcionario->nome }}" readonly>
      </div>
      <div class="form-group col-md-6">
        <label for="setor">Setor</label>
        <input type="text" class="form-control" name="setor" value="{{ $protocolo->setor->descricao }}" readonly>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="protocolo_tipo">Tipo do Protocolo</label>
        <input type="text" class="form-control" name="protocolo_tipo" value="{{ $protocolo->protocoloTipo->descricao }}" readonly>
      </div>
      <div class="form-group col-md-4">
        <label for="protocolo_situacao">Situação do Protocolo</label>
        <input type="text" class="form-control" name="protocolo_situacao" value="{{ $protocolo->protocoloSituacao->descricao }}" readonly>
      </div>      
    </div>
    <div class="form-group">
      <label for="descricao">Observações</label>
      <textarea class="form-control" name="descricao" rows="3" readonly>{{ $protocolo->descricao }}</textarea>      
    </div>
  </form>

  @if (count($periodos))
  <br>
  <div class="container bg-primary text-white">
    <p class="text-center">Períodos</p>
  </div>
  <div class="container">
    <div class="table-responsive">
      <table class="table table-striped">
          <thead>
              <tr>
                  <th scope="col">Inicial</th>
                  <th scope="col">Final</th>
                  <th scope="col">Tipo</th>
              </tr>
          </thead>
          <tbody>
              @foreach($periodos as $periodo)
              <tr>
                  <td>{{ isset($periodo->inicio) ?  $periodo->inicio->format('d/m/Y') : '-' }}</td>
                  <td>{{ isset($periodo->fim) ?  $periodo->fim->format('d/m/Y') : '-' }}</td>
                  <td>{{ $periodo->periodoTipo->descricao }}</td>
              </tr>    
              @endforeach                                                 
          </tbody>
      </table>
    </div>  
  </div>
  @endif
  @if (count($tramitacoes))
  <br>
  <div class="container bg-primary text-white">
    <p class="text-center">Tramitações</p>
  </div>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
          <tr>
              <th scope="col">Data</th>
              <th scope="col">Hora</th>
              <th scope="col">Funcionario</th>
              <th scope="col">Matrícula</th>
              <th scope="col">Setor</th>
              <th scope="col">Código</th>
              <th scope="col">Descrição</th>
          </tr>
      </thead>
      <tbody>
          @foreach($tramitacoes as $tramitacao)
          <tr>
              <td>{{ $tramitacao->created_at->format('d/m/Y')  }}</td>
              <td>{{ $tramitacao->created_at->format('H:i') }}</td>
              <td>{{ isset($tramitacao->funcionario_id) ?  $tramitacao->funcionario->nome : '-' }}</td>
              <td>{{ isset($tramitacao->funcionario_id) ?  $tramitacao->funcionario->matricula : '-' }}</td>
              <td>{{ isset($tramitacao->setor_id) ?  $tramitacao->setor->descricao : '-' }}</td>
              <td>{{ isset($tramitacao->setor_id) ?  $tramitacao->setor->codigo : '-' }}</td>
              <td>{{ $tramitacao->descricao }}</td>
          </tr>    
          @endforeach                                                 
      </tbody>
    </table>
  </div>
  @endif
  <br>
  <div class="container">
    <form method="post" action="{{route('protocolos.destroy', $protocolo->id)}}">
      @csrf
      @method('DELETE')
      <a href="{{ route('protocolos.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
      <a href="{{ route('protocolos.export.pdf.individual', $protocolo->id) }}" class="btn btn-primary" role="button"><i class="fas fa-print"></i> exportar para PDF</i></a>
      <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
    </form>
  </div>
</div>

@endsection
