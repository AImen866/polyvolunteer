@extends('layouts.app')
@section('title', 'Вход')
@section('content')
<div class="container" style="max-width:480px;padding-top:30px">
  <div class="panel">
    <h2 class="title">Вход</h2>
    <form method="POST" action="{{ route('login') }}">@csrf
        <label for="email">Почта</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        @error('email')<div class="err">{{ $message }}</div>@enderror
        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>
        <label class="check"><input type="checkbox" name="remember" value="1"> Запомнить меня</label>
        <p><button class="btn">Войти</button></p>
    </form>
    <p class="muted">Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
  </div>
</div>
@endsection
