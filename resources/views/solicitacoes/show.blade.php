@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('solicitacoes.index') }}">Lista de Solicitações</a></li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form>
    <div class="form-row">
      <div class="form-group col-md-3">
        <div class="p-3 bg-primary text-white text-right h2">Nº {{ $solicitacao->id }}</div>    
      </div>
      <div class="form-group col-md-2">
        <label for="dia">Data</label>
        <input type="text" class="form-control" name="dia" value="{{ $solicitacao->created_at->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora">Hora</label>
        <input type="text" class="form-control" name="hora" value="{{ $solicitacao->created_at->format('H:i') }}" readonly>
      </div>
      <div class="form-group col-md-5">
        <label for="setor">Operador</label>
        <input type="text" class="form-control" name="setor" value="{{ $solicitacao->user->name }}" readonly>
      </div>
    </div>
    <div class="form-group">
      <label for="remetente">Remetente(s)/Assunto</label>
      <textarea class="form-control" name="remetente" rows="3" readonly>{{ $solicitacao->remetente }}</textarea>      
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="identificacao">Identificacao</label>
        <input type="text" class="form-control" name="identificacao" value="{{ $solicitacao->identificacao }}" readonly>
      </div>
      <div class="form-group col-md-4">
        <label for="memorando_tipo">Tipo de Solicitação</label>
        <input type="text" class="form-control" name="memorando_tipo" value="{{ $solicitacao->solicitacaoTipo->descricao }}" readonly>
      </div>
      <div class="form-group col-md-4">
        <label for="memorando_situacao">Situação de Solicitação</label>
        <input type="text" class="form-control" name="memorando_situacao" value="{{ $solicitacao->solicitacaoSituacao->descricao }}" readonly>
      </div>      
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3" readonly>{{ $solicitacao->observacao }}</textarea>      
    </div>
  </form>

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
              <th scope="col">Observações</th>
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
    <form method="post" action="{{route('solicitacoes.destroy', $solicitacao->id)}}">
      @csrf
      @method('DELETE')
      <a href="{{ route('solicitacoes.index') }}" class="btn btn-primary" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
      <a href="{{ route('solicitacoes.export.pdf.individual', $solicitacao->id) }}" class="btn btn-primary" role="button"><i class="fas fa-print"></i> Exportar para PDF</a>
      <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Excluir</button>
    </form>
  </div>
</div>

@endsection
