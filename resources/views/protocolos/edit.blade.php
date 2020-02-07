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
      <li class="breadcrumb-item"><a href="{{ route('protocolos.index') }}">Lista de Protocolos</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
<div class="container">
  @if(Session::has('edited_protocolo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_protocolo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(Session::has('create_protocolo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_protocolo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if ($errors->has('resposta_id'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Erro!</strong>  {{ $errors->first('resposta_id') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif 
  @if ($errors->has('grupo_id'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Erro!</strong>  {{ $errors->first('grupo_id') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif 
  <form method="POST" action="{{ route('protocolos.update', $protocolo->id) }}">
    @csrf
    @method('PUT')
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
        <input type="hidden" id="funcionario_id" name="funcionario_id" value="{{ $protocolo->funcionario_id }}">
      </div>
      <div class="form-group col-md-6">
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
        <label for="protocolo_situacao">Situação do Protocolo</label>
        <input type="text" class="form-control" name="protocolo_situacao" value="{{ $protocolo->protocoloSituacao->descricao }}" readonly>
      </div>      
    </div>
    <div class="form-group">
      <label for="descricao">Observações</label>
      <textarea class="form-control" name="descricao" rows="3">{{ $protocolo->descricao }}</textarea>      
    </div>

    @if ($protocolo->grupo_id > 1)
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="encaminhado">Encaminhado para:</label>
          <input type="text" class="form-control" name="encaminhado" value="{{ $protocolo->grupo->descricao }}" readonly>  
      </div>
      <div class="form-group col-md-2">
        <label for="dia_encaminhamento">Data</label>
        <input type="text" class="form-control" name="dia_encaminhamento" value="{{ $protocolo->encaminhado_em->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora_encaminhamento">Hora</label>
        <input type="text" class="form-control" name="hora_encaminhamento" value="{{ $protocolo->encaminhado_em->format('H:i') }}" readonly>
      </div>
    </div>  
    @endif


    @if ($protocolo->concluido == 's')
    <div class="form-row">
      <div class="form-group col-md-8">
        <label for="resposta">Resposta da conclusão:</label>
          <input type="text" class="form-control" name="resposta" value="{{ $protocolo->resposta->descricao }}" readonly>  
      </div>
      <div class="form-group col-md-2">
        <label for="dia_resposta">Data</label>
        <input type="text" class="form-control" name="dia_resposta" value="{{ $protocolo->concluido_em->format('d/m/Y') }}" readonly>
      </div>
      <div class="form-group col-md-2">
        <label for="hora_resposta">Hora</label>
        <input type="text" class="form-control" name="hora_resposta" value="{{ $protocolo->concluido_em->format('H:i') }}" readonly>
      </div>
    </div>  
    <div class="form-group">
      <label for="mensagem_conclusao">Mensagem de conclusão</label>
      <textarea class="form-control" name="mensagem_conclusao" rows="3">{{ $protocolo->concluido_mensagem }}</textarea>      
    </div>    
    @endif


    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar</button>

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEncaminhar">
        <i class="fas fa-hand-point-right"></i> Encaminhar
    </button>

    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalConcluir">
        <i class="fas fa-thumbs-up"></i> Concluir
    </button>  

    <a href="{{ route('protocolos.export.pdf.individual', $protocolo->id) }}" class="btn btn-primary" role="button"><i class="fas fa-print"></i> Imprimir</i></a>
  </form>
</div>
<br>
<div class="container bg-primary text-white">
  <p class="text-center">Períodos</p>
</div>
<div class="container">
  <div class="row">
    <div class="col-md-4">
      <form method="POST" action="{{ route('periodos.store') }}">
        @csrf
        <input type="hidden" id="protocolo_id" name="protocolo_id" value="{{ $protocolo->id }}">
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
          <label for="periodo_tipo_id">Tipo do período</label>
          <select class="form-control {{ $errors->has('periodo_tipo_id') ? ' is-invalid' : '' }}" name="periodo_tipo_id" id="periodo_tipo_id">
            <option value="" selected>Selecionar ... </option>
            @foreach($periodotipos as $periodotipo)
            <option value="{{$periodotipo->id}}">{{$periodotipo->descricao}}</option>
            @endforeach
          </select>
          @if ($errors->has('periodo_tipo_id'))
          <div class="invalid-feedback">
          {{ $errors->first('periodo_tipo_id') }}
          </div>
          @endif         
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Incluir Período</button>    
      </form>  
    </div>
    <div class="col-md-8">
      @if(Session::has('create_periodo'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Info!</strong>  {{ session('create_periodo') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('delete_periodo'))
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Info!</strong>  {{ session('delete_periodo') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif      
      <div class="table-responsive">
          <table class="table table-striped">
              <thead>
                  <tr>
                      <th scope="col">Inicial</th>
                      <th scope="col">Final</th>
                      <th scope="col">Tipo</th>
                      <th scope="col"></th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($periodos as $periodo)
                  <tr>
                      <td>{{ isset($periodo->inicio) ?  $periodo->inicio->format('d/m/Y') : '-' }}</td>
                      <td>{{ isset($periodo->fim) ?  $periodo->fim->format('d/m/Y') : '-' }}</td>
                      <td>{{ $periodo->periodoTipo->descricao }}</td>
                      <td>
                        <form method="post" action="{{route('periodos.destroy', $periodo->id)}}">
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
  </div>
</div>
<br>
<div class="container bg-primary text-white">
  <p class="text-center">Tramitações</p>
</div>
<div class="container">
  <form method="POST" action="{{ route('tramitacoes.store') }}">
    @csrf
    <input type="hidden" id="protocolo_id" name="protocolo_id" value="{{ $protocolo->id }}">
    
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="funcionario_tramitacao">Funcionário</label>
        <input type="text" class="form-control typeahead" name="funcionario_tramitacao" id="funcionario_tramitacao" value="{{ old('funcionario_tramitacao') ?? '' }}" autocomplete="off">
        <input type="hidden" id="funcionario_tramitacao_id" name="funcionario_tramitacao_id" value="{{ old('funcionario_tramitacao_id') ?? '' }}">
      </div>
      
      <div class="form-group col-md-2">
        <label for="funcionario_tramitacao_matricula">Matrícula</label>
        <input type="text" class="form-control" name="funcionario_tramitacao_matricula" id="funcionario_tramitacao_matricula" value="" readonly tabIndex="-1" placeholder="">
      </div>

      <div class="form-group col-md-4">
        <label for="setor_tramitacao">Setor</label>
        <input type="text" class="form-control" name="setor_tramitacao" id="setor_tramitacao" value="{{ old('setor_tramitacao') ?? '' }}" autocomplete="off">
        <input type="hidden" id="setor_tramitacao_id" name="setor_tramitacao_id" value="{{ old('setor_tramitacao_id') ?? '' }}">
      </div>

      <div class="form-group col-md-2">
        <label for="setor_tramitacao_codigo">Código</label>
        <input type="text" class="form-control" name="setor_tramitacao_codigo" id="setor_tramitacao_codigo" value="" readonly tabIndex="-1" placeholder="">
      </div>

    </div>



    <div class="form-group">
        <label for="descricao">Observações</label>
        <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao') ?? '' }}" autocomplete="off">    
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-plus-square"></i> Incluir Tramitação</button>
  </form>  
</div>
<div class="container">
  @if(Session::has('create_tramitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_tramitacao') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(Session::has('delete_tramitacao'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('delete_tramitacao') }}
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
            @foreach($tramitacoes as $tramitacao)
            <tr>
                <td>{{ $tramitacao->created_at->format('d/m/Y')  }}</td>
                <td>{{ $tramitacao->created_at->format('H:i') }}</td>
                <td>{{ isset($tramitacao->funcionario_id) ?  $tramitacao->funcionario->nome : '-' }}</td>
                <td>{{ isset($tramitacao->funcionario_id) ?  $tramitacao->funcionario->matricula : '-' }}</td>
                <td>{{ isset($tramitacao->setor_id) ?  $tramitacao->setor->descricao : '-' }}</td>
                <td>{{ isset($tramitacao->setor_id) ?  $tramitacao->setor->codigo : '-' }}</td>
                <td>{{ $tramitacao->descricao }}</td>
                <td>
                  <form method="post" action="{{route('tramitacoes.destroy', $tramitacao->id)}}">
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
<div class="container bg-primary text-white">
  <p class="text-center">Anexos</p>
</div>
<div class="container">
  <form method="POST" action="{{ route('protocoloanexos.store') }}" class="form-inline" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="protocolo_id" name="protocolo_id" value="{{ $protocolo->id }}">
    <div class="form-group">
      <label for="arquivo">Escolha o arquivo</label>
      <input type="file" class="form-control-file  {{ $errors->has('arquivo') ? ' is-invalid' : '' }}" id="arquivo" name="arquivo">
      @if ($errors->has('arquivo'))
      <div class="invalid-feedback">
      {{ $errors->first('arquivo') }}
      </div>
      @endif
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-paperclip"></i> Anexar Arquivo</button>
  </form>  
</div>
<div class="container">
  @if(Session::has('create_anexo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('create_anexo') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(Session::has('delete_anexo'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('delete_anexo') }}
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
                <th scope="col">Operador</th>
                <th scope="col">Arquivo</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($anexos as $anexo)
            <tr>
                <td>{{ $anexo->created_at->format('d/m/Y')  }}</td>
                <td>{{ $anexo->created_at->format('H:i') }}</td>
                <td>{{ $anexo->user->name }}</td>
                <td><a href="{{ $anexo->arquivoUrl }}" target="_blank">{{ $anexo->arquivoNome }}</a></td>
                <td>
                  <form method="post" action="{{route('protocoloanexos.destroy', $anexo->id)}}"  onsubmit="return confirm('Você tem certeza que quer excluir esse arquivo?');">
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
    <a href="{{ route('protocolos.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>


<div class="modal fade" id="modalEncaminhar" tabindex="-1" role="dialog" aria-labelledby="JanelaEncaminhar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-hand-point-right"></i> Encaminhar Protocolo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('protocolos.encaminhar', $protocolo->id) }}">
          @csrf      
          <div class="form-group">
            <label for="grupo_id">Selecione o Grupo de Trabalho</label>
            <select class="form-control" name="grupo_id" id="grupo_id">
              <option value="" selected="true">Selecione ...</option>        
              @foreach($grupos as $grupo)
              <option value="{{$grupo->id}}">{{$grupo->descricao}}</option>
              @endforeach
            </select>  
          </div>
          <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="fas fa-hand-point-right"></i> Encaminhar?</button>
          </div>
        </form>
      </div>     
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalConcluir" tabindex="-1" role="dialog" aria-labelledby="JanelaConcluir" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-thumbs-up"></i> Concluir Protocolo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('protocolos.concluir', $protocolo->id) }}">
          @csrf
          <div class="form-group">
            <label for="resposta_id">Selecione a resposta de conclusão desse protocolo</label>
            <select class="form-control" name="resposta_id" id="resposta_id">
              <option value="" selected="true">Selecione ...</option>        
              @foreach($respostas as $resposta)
              <option value="{{$resposta->id}}">{{$resposta->descricao}}</option>
              @endforeach
            </select>  
          </div>
          <div class="form-group">
            <label for="concluido_mensagem">Mensagem de conclusão(opcional):</label>
            <textarea class="form-control" name="concluido_mensagem" rows="3"></textarea>      
          </div>
          <div class="form-group">
              <button type="submit" class="btn btn-primary"><i class="fas fa-hand-point-right"></i> Concluir?</button>
          </div>
        </form>
      </div>     
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-window-close"></i> Fechar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script-footer')
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('locales/bootstrap-datepicker.pt-BR.min.js') }}"></script>
<script src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
<script>
$(document).ready(function(){

    $('#dtainicio').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        clearBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true
    });

    $('#dtafinal').datepicker({
        format: "dd/mm/yyyy",
        todayBtn: "linked",
        clearBtn: true,
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true
    });

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
                source: funcionarios.ttAdapter(),
                templates: {
                  empty: [
                    '<div class="empty-message">',
                      '<p class="text-center font-weight-bold text-warning">Não foi encontrado nenhum funcionário com o texto digitado.</p>',
                    '</div>'
                  ].join('\n'),
                  suggestion: function(data) {
                      return '<div><div>' + data.text + ' - <strong>Matrícula:</strong> ' + data.matricula + '</div></div>';
                    }
                }
        }).on("typeahead:selected", function(obj, datum, name) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#funcionario_tramitacao_id').val(datum.value);
            $('#funcionario_tramitacao_matricula').val(datum.matricula);
        }).on('typeahead:autocompleted', function (e, datum) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#funcionario_tramitacao_id').val(datum.value);
            $('#funcionario_tramitacao_matricula').val(datum.matricula);
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
        source: setores.ttAdapter(),
        templates: {
          empty: [
            '<div class="empty-message">',
              '<p class="text-center font-weight-bold text-warning">Não foi encontrado nenhum setor com o texto digitado.</p>',
            '</div>'
          ].join('\n'),
          suggestion: function(data) {
              return '<div><div>' + data.text + ' - <strong>Código:</strong> ' + data.codigo + '</div></div>';
            }
        }
        }).on("typeahead:selected", function(obj, datum, name) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#setor_tramitacao_id').val(datum.value);
            $('#setor_tramitacao_codigo').val(datum.codigo);
        }).on('typeahead:autocompleted', function (e, datum) {
            console.log(datum);
            $(this).data("seletectedId", datum.value);
            $('#setor_tramitacao_id').val(datum.value);
            $('#setor_tramitacao_codigo').val(datum.codigo);
    });
}); 
</script>
@endsection
