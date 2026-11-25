@extends('layouts.app')
@section('title', 'Все мероприятия')
@section('content')
<section class="block"><div class="container">
  <p><a href="{{ route('admin.dashboard') }}">← Панель</a></p>
  <h2 class="title">Все мероприятия</h2>
  <div class="panel"><table><thead><tr><th>Название</th><th>Организатор</th><th>Дата</th><th>Заявки</th><th>Статус</th></tr></thead><tbody>
  @foreach($events as $e)
    <tr><td><a href="{{ route('events.show', $e) }}">{{ $e->title }}</a></td><td>{{ $e->organizer->name }}</td>
      <td>{{ $e->starts_at->translatedFormat('j F Y') }}</td><td>{{ $e->taken }} / {{ $e->capacity }}</td>
      <td><span class="badge {{ $e->status }}">{{ \App\Models\Event::STATUSES[$e->status] }}</span></td></tr>
  @endforeach</tbody></table></div>
  {{ $events->links() }}
</div></section>
@endsection
