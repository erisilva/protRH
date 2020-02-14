@extends('layouts.public')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">Memorando RH SMS</li>
      <li class="breadcrumb-item active" aria-current="page">Exibir Memorando</li>
    </ol>
  </nav>
</div>
<div class="container">
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Atenção!</strong> Para sua segurança <strong>não</strong> compartilhe, salve ou envie por e-mail esse link (endereço de página).
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</div>  
<div class="container">
  <form>
    <div class="form-row">
      <div class="form-group col-md-3">
        <div class="p-3 bg-primary text-white text-right h2">Nº(RH) {{ $memorando->id }}</div>    
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
      <label for="remetente">Remetente(s)/Assunto</label>
      <textarea class="form-control" name="remetente" rows="3" readonly>{{ $memorando->remetente }}</textarea>      
    </div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="protocolo_tipo">Tipo do Memorando</label>
        <input type="text" class="form-control" name="protocolo_tipo" value="{{ $memorando->memorandoTipo->descricao }}" readonly>
      </div>
      <div class="form-group col-md-6">
        <label for="protocolo_situacao">Situação do Memorando</label>
        <input type="text" class="form-control font-weight-bold" name="protocolo_situacao" value="{{ $memorando->memorandoSituacao->descricao }}" readonly>
      </div>      
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3" readonly>{{ $memorando->observacao }}</textarea>      
    </div>
    @if ($memorando->concluido == 's')
    <div class="container bg-primary text-white py-3">
      <div class="form-row">
        <div class="form-group col-md-8">
          <label for="resposta">Solução</label>
            <input type="text" class="form-control font-weight-bold" name="resposta" value="{{ $memorando->resposta->descricao }}" readonly>  
        </div>
        <div class="form-group col-md-2">
          <label for="dia_resposta">Data</label>
          <input type="text" class="form-control" name="dia_resposta" value="{{ $memorando->concluido_em->format('d/m/Y') }}" readonly>
        </div>
        <div class="form-group col-md-2">
          <label for="hora_resposta">Hora</label>
          <input type="text" class="form-control" name="hora_resposta" value="{{ $memorando->concluido_em->format('H:i') }}" readonly>
        </div>
      </div>  
      <div class="form-group">
        <label for="mensagem_conclusao">Mensagem</label>
        <textarea class="form-control" name="mensagem_conclusao" rows="3">{{ $memorando->concluido_mensagem }}</textarea>      
      </div>    
    </div>
    @endif

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
</div>

@endsection
