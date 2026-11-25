@extends('layouts.app')
@section('title', 'Категории')
@section('content')
<section class="block"><div class="container">
  <p><a href="{{ route('admin.dashboard') }}">← Панель</a></p>
  <h2 class="title">Категории мероприятий</h2>
  <div class="panel"><table><thead><tr><th>Название</th><th>Коэфф. баллов</th><th>Гостей на волонтёра</th><th>Событий</th><th></th></tr></thead><tbody>
  @foreach($categories as $c)
    <tr>
      <td><input form="cu{{ $c->id }}" name="name" value="{{ $c->name }}"></td>
      <td><input form="cu{{ $c->id }}" name="hours_coefficient" type="number" step="0.1" value="{{ $c->hours_coefficient }}" style="width:90px"></td>
      <td><input form="cu{{ $c->id }}" name="guests_per_volunteer" type="number" value="{{ $c->guests_per_volunteer }}" style="width:90px"></td>
      <td>{{ $c->events_count }}</td>
      <td><div class="actions-cell">
        <form id="cu{{ $c->id }}" method="POST" action="{{ route('admin.categories.update', $c) }}">@csrf @method('PUT')<button class="btn small">Сохранить</button></form>
        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="Удалить категорию?">@csrf @method('DELETE')<button class="btn small danger">Удалить</button></form></div></td></tr>
  @endforeach</tbody></table></div>
  <div class="panel"><h3 style="margin-top:0">Новая категория</h3>
    @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('admin.categories.store') }}" class="row row3">@csrf
      <div><label>Название</label><input name="name" required></div>
      <div><label>Коэффициент баллов</label><input name="hours_coefficient" type="number" step="0.1" value="1.0"></div>
      <div><label>Гостей на волонтёра</label><input name="guests_per_volunteer" type="number" value="20"></div>
      <div><button class="btn">Добавить</button></div>
    </form></div>
</div></section>
@endsection
