@extends('layouts.app')
@section('title', 'Администрирование')
@section('content')
<section class="block"><div class="container">
  <h2 class="title">Панель администратора</h2>
  <div class="stats" style="margin:0 0 24px">@foreach($counts as $k => $v)<div class="stat"><b>{{ $v }}</b><span>{{ $k }}</span></div>@endforeach</div>
  <div class="grid">
    <a class="panel" href="{{ route('admin.users') }}"><b>Пользователи</b><div class="meta">Роли и удаление аккаунтов</div></a>
    <a class="panel" href="{{ route('admin.events') }}" style="margin:0"><b>Мероприятия</b><div class="meta">Все события платформы</div></a>
    <a class="panel" href="{{ route('admin.articles.index') }}" style="margin:0"><b>Статьи</b><div class="meta">Публикация и редактирование</div></a>
    <a class="panel" href="{{ route('admin.categories.index') }}" style="margin:0"><b>Категории</b><div class="meta">Коэффициенты баллов и нормы</div></a>
  </div>
</div></section>
@endsection
