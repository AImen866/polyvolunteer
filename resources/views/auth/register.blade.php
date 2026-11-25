@extends('layouts.app')
@section('title', 'Регистрация')
@section('content')
<div class="container" style="max-width:640px;padding-top:30px">
  <div class="panel">
    <h2 class="title">Регистрация</h2>
    <form method="POST" action="{{ route('register') }}">@csrf
        <label for="role">Я хочу зарегистрироваться как</label>
        <select id="role" name="role">
            <option value="volunteer" @selected(old('role') === 'volunteer')>Волонтёр</option>
            <option value="organizer" @selected(old('role') === 'organizer')>Организатор мероприятий</option>
        </select>
        <label for="name">ФИО</label>
        <input id="name" name="name" value="{{ old('name') }}" required>
        @error('name')<div class="err">{{ $message }}</div>@enderror
        <label for="email">Почта</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        @error('email')<div class="err">{{ $message }}</div>@enderror
        <div id="vol-box" class="row">
            <div><label for="faculty">Факультет</label><input id="faculty" name="faculty" value="{{ old('faculty') }}"></div>
            <div><label for="group_name">Группа</label><input id="group_name" name="group_name" value="{{ old('group_name') }}" placeholder="241-321"></div>
        </div>
        <div id="org-box" class="hide">
            <label for="organization">Организация (студсовет, кафедра, КТД)</label>
            <input id="organization" name="organization" value="{{ old('organization') }}">
            @error('organization')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div><label for="password">Пароль</label><input id="password" name="password" type="password" required>
                @error('password')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="password_confirmation">Повторите пароль</label><input id="password_confirmation" name="password_confirmation" type="password" required></div>
        </div>
        <p><button class="btn">Создать аккаунт</button></p>
    </form>
  </div>
</div>
@endsection
