@extends('mails.mail.layout')

@section('header')
    @parent
@endsection

@section('content')
    <h1>Olá, <b>{{ $name }}</b>!</h1>

    <p class="separator"></p>

    <p style="text-align: center">
        Clique no botão abaixo para definir sua senha.
    </p>

    <p style="text-align: center; word-break: break-word">
        <a href="{{ $link }}" class="button button-primary" target="_blank">
            Ir para definição de senha
        </a>
    </p>

    <p style="text-align: center; word-break: break-word; font-size: 12px">
        Ou acesse o link:
        <br>
        <a href="{{ $link }}" target="_blank">
            {{ $link }}
        </a>
    </p>
@endsection

@section('footer')
    @parent
@endsection

