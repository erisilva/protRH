@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Bem Vindo!</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            Você está logado no sistema! 
                        </div>
                    @endif

                    <figure class="figure">
                      <img src="{{ asset('img/protocolo.jpg') }}" class="figure-img img-fluid rounded" alt="home">
                      <figcaption class="figure-caption">Sistema de protocolos do RH</figcaption>
                    </figure>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
