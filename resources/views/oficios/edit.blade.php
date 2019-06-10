@extends('layouts.app')

@section('css-header')
<link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
<style>
  .twitter-typeahead, .tt-hint, .tt-input, .tt-menu { width: 100%; }
  .tt-query, .tt-hint { outline: none;}
  .tt-query { box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);}
  .tt-hint {color: #999;}
  .tt-menu { 
      width: 100%;
      margin-top: 12px;
      padding: 8px 0;
      background-color: #fff;
      border: 1px solid #ccc;
      border: 1px solid rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      box-shadow: 0 5px 10px rgba(0,0,0,.2);
  }
  .tt-suggestion { padding: 3px 20px; }
  .tt-suggestion.tt-is-under-cursor { color: #fff; }
  .tt-suggestion p { margin: 0;}
</style>
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('oficios.index') }}">Lista de Ofícios</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  {{-- avisa se uma permissão foi alterada --}}
  @if(Session::has('edited_oficio'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_oficio') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(Session::has('create_oficio'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_oficio') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <form method="POST" action="{{ route('oficios.update', $oficio->id) }}">
    @csrf
    @method('PUT')
      <div class="form-row">
      <div class="form-group col-md-3">
        <div class="p-3 bg-primary text-white text-right h2">Nº {{ $oficio->id }}</div>    
      </div>
      <div class="form-group col-md-2">
        <label for="dia">Data</label>
        <input type="text" class="form-control" name="dia" value="{{ $oficio->created_at->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora">Hora</label>
        <input type="text" class="form-control" name="hora" value="{{ $oficio->created_at->format('H:i') }}" readonly>
      </div>
      <div class="form-group col-md-5">
        <label for="setor">Operador</label>
        <input type="text" class="form-control" name="setor" value="{{ $oficio->user->name }}" readonly>
      </div>
    </div>
    <div class="form-group">
      <label for="remetente">Remetente(s)/Assunto</label>
      <textarea class="form-control{{ $errors->has('remetente') ? ' is-invalid' : '' }}" name="remetente" rows="3">{{ old('remetente') ?? $oficio->remetente }}</textarea>
      @if ($errors->has('remetente'))
      <div class="invalid-feedback">
      {{ $errors->first('remetente') }}
      </div>
      @endif  
    </div>
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="oficio_tipo_id">Tipo de Memorando</label>
        <select class="form-control" name="oficio_tipo_id" id="oficio_tipo_id">
          <option value="{{ $oficio->oficio_tipo_id }}" selected="true"> &rarr; {{ $oficio->oficioTipo->descricao }}</option>        
          @foreach($oficiotipos as $oficiotipo)
          <option value="{{$oficiotipo->id}}">{{$oficiotipo->descricao}}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group col-md-6">
        <label for="oficio_situacao_id">Situação de Memorando</label>
        <select class="form-control" name="oficio_situacao_id" id="oficio_situacao_id">
          <option value="{{$oficio->oficio_situacao_id}}" selected="true"> &rarr; {{ $oficio->oficioSituacao->descricao }}</option>
          @foreach($oficiosituacoes as $oficiosituacao)
          <option value="{{$oficiosituacao->id}}">{{$oficiosituacao->descricao}}</option>
          @endforeach
        </select>
      </div>      
    </div>
    <div class="form-group">
      <label for="observacao">Observações</label>
      <textarea class="form-control" name="observacao" rows="3">{{ old('observacao') ?? $oficio->observacao }}</textarea>      
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar Dados do Ofício</button>
    <a href="{{ route('oficios.export.pdf.individual', $oficio->id) }}" class="btn btn-primary" role="button"><i class="fas fa-print"></i> exportar para PDF</i></a>
  </form>
</div>
<br>
<div class="container bg-primary text-white">
  <p class="text-center">Tramitações</p>
</div>
<div class="container">
  <form method="POST" action="{{ route('oficiotramitacoes.store') }}">
    @csrf
    <input type="hidden" id="oficio_id" name="oficio_id" value="{{ $oficio->id }}">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="funcionario_tramitacao">Funcionário</label>
        <input type="text" class="form-control typeahead" name="funcionario_tramitacao" id="funcionario_tramitacao" value="{{ old('funcionario_tramitacao') ?? '' }}" autocomplete="off">
        <input type="hidden" id="funcionario_tramitacao_id" name="funcionario_tramitacao_id" value="{{ old('funcionario_tramitacao_id') ?? '' }}">
      </div>
      <div class="form-group col-md-6">
        <label for="setor_tramitacao">Setor</label>
        <input type="text" class="form-control" name="setor_tramitacao" id="setor_tramitacao" value="{{ old('setor_tramitacao') ?? '' }}" autocomplete="off">
        <input type="hidden" id="setor_tramitacao_id" name="setor_tramitacao_id" value="{{ old('setor_tramitacao_id') ?? '' }}">
      </div>
    </div>
    <div class="form-group">
      <label for="descricao">Observações</label>
      <textarea class="form-control" name="descricao" rows="3">{{ old('descricao') ?? '' }}</textarea> 
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Tramitação</button>
  </form>
</div>
<br>
<div class="container">
  @if(Session::has('create_oficiotramitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_oficiotramitacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(Session::has('delete_oficiotramitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('delete_oficiotramitacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
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
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($oficiotramitacoes as $oficiotramitacao)
            <tr>
                <td>{{ $oficiotramitacao->created_at->format('d/m/Y')  }}</td>
                <td>{{ $oficiotramitacao->created_at->format('H:i') }}</td>
                <td>{{ isset($oficiotramitacao->funcionario_id) ?  $oficiotramitacao->funcionario->nome : '-' }}</td>
                <td>{{ isset($oficiotramitacao->funcionario_id) ?  $oficiotramitacao->funcionario->matricula : '-' }}</td>
                <td>{{ isset($oficiotramitacao->setor_id) ?  $oficiotramitacao->setor->descricao : '-' }}</td>
                <td>{{ isset($oficiotramitacao->setor_id) ?  $oficiotramitacao->setor->codigo : '-' }}</td>
                <td>{{ $oficiotramitacao->descricao }}</td>
                <td>
                  <form method="post" action="{{route('oficiotramitacoes.destroy', $oficiotramitacao->id)}}">
                    @csrf
                    @method('DELETE')  
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                  </form>
                </td>
            </tr>    
            @endforeach                                                 
        </tbody>
    </table>
  </div> 
</div>
<br>
<div class="container">
  <div class="float-right">
    <a href="{{ route('oficios.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection

@section('script-footer')
<script src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
<script>
$(document).ready(function(){

    var funcionarios = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace("text"),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "{{route('funcionarios.autocomplete')}}?query=%QUERY",
            wildcard: '%QUERY'
        },
        limit: 10
    });
    funcionarios.initialize();

    $("#funcionario_tramitacao").typeahead({
        hint: true,
        highlight: true,
        minLength: 1
    },
    {
        name: "funcionarios",
        displayKey: "text",
        source: funcionarios.ttAdapter()
        }).on("typeahead:selected", function(obj, datum, name) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#funcionario_tramitacao_id').val(datum.value);
            console.log(datum.value);
        }).on('typeahead:autocompleted', function (e, datum) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#funcionario_tramitacao_id').val(datum.value);
            console.log(datum.value);
    });

    var setores = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace("text"),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: "{{route('setores.autocomplete')}}?query=%QUERY",
            wildcard: '%QUERY'
        },
        limit: 10
    });
    setores.initialize();

    $("#setor_tramitacao").typeahead({
        hint: true,
        highlight: true,
        minLength: 1
    },
    {
        name: "setores",
        displayKey: "text",
        source: setores.ttAdapter()
        }).on("typeahead:selected", function(obj, datum, name) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#setor_tramitacao_id').val(datum.value);
            console.log(datum.value);
        }).on('typeahead:autocompleted', function (e, datum) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#setor_tramitacao_id').val(datum.value);
            console.log(datum.value);
    });
});
</script>
@endsection