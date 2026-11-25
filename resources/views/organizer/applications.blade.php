@extends('layouts.app')
@section('title', 'Заявки на мероприятие')
@section('content')
<section class="block"><div class="container">
  <p><a href="{{ route('organizer.events') }}">← К моим мероприятиям</a></p>
  <h2 class="title">Заявки: {{ $event->title }}</h2>
  <div class="panel">
    <p class="meta">{{ $event->starts_at->translatedFormat('j F Y, H:i') }} · занято {{ $event->taken }} из {{ $event->capacity }} мест</p>
  @if($event->registrations->isEmpty())<div class="empty">Заявок пока нет.</div>
  @else
  <table><thead><tr><th>Волонтёр</th><th>Группа</th><th>Мотивация</th><th>Статус</th><th>Действия</th></tr></thead><tbody>
  @foreach($event->registrations as $r)
    <tr><td>{{ $r->user->name }}<div class="meta">{{ $r->user->email }}{{ $r->user->phone ? ', '.$r->user->phone : '' }}</div></td>
        <td>{{ $r->user->group_name ?: '—' }}</td>
        <td style="max-width:260px">{{ $r->motivation ?: '—' }}</td>
        <td><span class="badge {{ $r->status }}">{{ $r->status_label }}</span></td>
        <td><div class="actions-cell">
        @foreach(['approved' => 'Одобрить', 'rejected' => 'Отклонить', 'attended' => 'Был на месте'] as $st => $label)
            @if($r->status !== $st)
            <form method="POST" action="{{ route('organizer.registrations.update', $r) }}">@csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ $st }}"><button class="btn small {{ $st === 'rejected' ? 'secondary' : '' }}">{{ $label }}</button></form>
            @endif
        @endforeach
        </div></td></tr>
  @endforeach</tbody></table>
  @endif
  </div>
</div></section>
@endsection
