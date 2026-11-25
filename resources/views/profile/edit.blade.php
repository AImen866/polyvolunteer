@extends('layouts.app')
@section('title', 'Редактирование профиля')
@section('content')
<div class="container" style="max-width:680px;padding-top:30px">
  <div class="panel">
    <h2 class="title">Редактирование профиля</h2>
    <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
        <label for="name">ФИО</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="err">{{ $message }}</div>@enderror
        <div class="row">
            <div><label for="email">Почта</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="phone">Телефон</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 900 000-00-00">
                @error('phone')<div class="err">{{ $message }}</div>@enderror</div>
        </div>
        @if($user->hasRole('volunteer'))
        <div class="row">
            <div><label for="faculty">Факультет</label><input id="faculty" name="faculty" value="{{ old('faculty', $user->faculty) }}"></div>
            <div><label for="group_name">Группа</label><input id="group_name" name="group_name" value="{{ old('group_name', $user->group_name) }}"></div>
        </div>
        @else
        <label for="organization">Организация</label><input id="organization" name="organization" value="{{ old('organization', $user->organization) }}">
        @endif
        <label for="bio">О себе</label><textarea id="bio" name="bio">{{ old('bio', $user->bio) }}</textarea>
        <div class="row">
            <div><label for="password">Новый пароль (если нужно сменить)</label><input id="password" name="password" type="password">
                @error('password')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="password_confirmation">Повторите пароль</label><input id="password_confirmation" name="password_confirmation" type="password"></div>
        </div>
        <p><button class="btn">Сохранить</button> <a class="btn secondary" href="{{ route('profile.show') }}">Отмена</a></p>
    </form>
  </div>
</div>
@endsection
