@extends('layouts.app')
@section('title', 'Мои мероприятия')
@section('content')
<section class="block"><div class="container">
  <div class="head-row"><h2 class="title">Мои мероприятия</h2><a class="btn" href="{{ route('organizer.events.create') }}">Создать мероприятие</a></div>
  <div class="panel">
  @if($events->isEmpty())<div class="empty">Мероприятий пока нет.</div>
  @else
  <table><thead><tr><th>Название</th><th>Дата</th><th>Заявки</th><th>Статус</th><th></th></tr></thead><tbody>
  @foreach($events as $e)
    <tr><td><a href="{{ route('events.show', $e) }}">{{ $e->title }}</a><div class="meta">{{ $e->category->name }}</div></td>
        <td>{{ $e->starts_at->translatedFormat('j F Y, H:i') }}</td>
        <td>{{ $e->taken }} из {{ $e->capacity }}</td>
        <td><span class="badge {{ $e->status }}">{{ \App\Models\Event::STATUSES[$e->status] }}</span></td>
        <td><div class="actions-cell">
            <a class="btn small" href="{{ route('organizer.applications', $e) }}">Заявки</a>
            <a class="btn small secondary" href="{{ route('organizer.events.edit', $e) }}">Изменить</a>
            <form method="POST" action="{{ route('organizer.events.destroy', $e) }}" data-confirm="Удалить мероприятие вместе с заявками?">@csrf @method('DELETE')<button class="btn small danger">Удалить</button></form>
        </div></td></tr>
  @endforeach</tbody></table>
  @endif
  </div>
</div></section>
@endsection
