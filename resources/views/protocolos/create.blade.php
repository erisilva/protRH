@extends('layouts.app')

@section('css-header')
    <style>
      .twitter-typeahead, .tt-hint, .tt-input, .tt-menu { width: 100%; }
      .tt-query,
      .tt-hint {
          outline: none;
      }

      .tt-query {
          box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
      }

      .tt-hint {
          color: #999;
      }

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

      .tt-suggestion {
          padding: 3px 20px;
      }

      .tt-suggestion.tt-is-under-cursor { 
          color: #fff;
      }

      .tt-suggestion p {
          margin: 0;
      }
    </style>
@endsection

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('protocolos.index') }}">Lista de Protocolos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Novo Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  <form method="POST" action="{{ route('protocolos.store') }}">
    @csrf
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="funcionario">Funcionário</label>
        <input type="text" class="form-control typeahead {{ $errors->has('funcionario_id') ? ' is-invalid' : '' }}" name="funcionario" id="funcionario" value="{{ old('funcionario') ?? '' }}" autocomplete="off">
        <input type="hidden" id="funcionario_id" name="funcionario_id" value="{{ old('funcionario_id') ?? '' }}">
      </div>
      <div class="form-group col-md-6">
        <label for="setor">Setor</label>
        <input type="text" class="form-control{{ $errors->has('setor_id') ? ' is-invalid' : '' }}" name="setor" id="setor" value="{{ old('setor') ?? '' }}" autocomplete="off">
        <input type="hidden" id="setor_id" name="setor_id" value="{{ old('setor_id') ?? '' }}">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="protocolo_tipo_id">Tipo do Protocolo</label>
        <select class="form-control {{ $errors->has('protocolo_tipo_id') ? ' is-invalid' : '' }}" name="protocolo_tipo_id" id="protocolo_tipo_id">
          <option value="" selected="true">Selecione ...</option>        
          @foreach($protocolotipos as $protocolotipo)
          <option value="{{$protocolotipo->id}}">{{$protocolotipo->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('protocolo_tipo_id'))
        <div class="invalid-feedback">
        {{ $errors->first('protocolo_tipo_id') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-4">
        <label for="protocolo_situacao_id">Situação do Protocolo</label>
        <select class="form-control {{ $errors->has('protocolo_situacao_id') ? ' is-invalid' : '' }}" name="protocolo_situacao_id" id="protocolo_situacao_id">
            <option value="" selected="true">Selecione ...</option>
          @foreach($protocolosituacoes as $protocolosituacao)
          <option value="{{$protocolosituacao->id}}">{{$protocolosituacao->descricao}}</option>
          @endforeach
        </select>
        @if ($errors->has('protocolo_situacao_id'))
        <div class="invalid-feedback">
        {{ $errors->first('protocolo_situacao_id') }}
        </div>
        @endif
      </div>      
    </div>  
    <div class="form-group">
      <label for="descricao">Observações</label>
      <textarea class="form-control" name="descricao" rows="3">{{ old('descricao') ?? '' }}</textarea>      
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Protocolo</button>
  </form>
  <div class="float-right">
    <a href="{{ route('protocolos.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
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

            $("#funcionario").typeahead({
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
                    $('#funcionario_id').val(datum.value);
                    console.log(datum.value);
                }).on('typeahead:autocompleted', function (e, datum) {
                    console.log(datum);
                    $(this).data("seletectedId", datum.value);
                    $('#funcionario_id').val(datum.value);
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

            $("#setor").typeahead({
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
                    $('#setor_id').val(datum.value);
                    console.log(datum.value);
                }).on('typeahead:autocompleted', function (e, datum) {
                    console.log(datum);
                    $(this).data("seletectedId", datum.value);
                    $('#setor_id').val(datum.value);
                    console.log(datum.value);
            });                      
        });
</script>
@endsection
