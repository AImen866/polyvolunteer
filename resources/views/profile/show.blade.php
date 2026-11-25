@extends('layouts.app')
@section('title', 'Профиль')
@section('content')
<section class="block"><div class="container">
  <div class="panel">
    <div class="profile-head">
        <div class="avatar">{{ mb_substr($user->name, 0, 1) }}</div>
        <div style="flex:1">
            <h2 style="margin:0;color:var(--navy)">{{ $user->name }}</h2>
            <div class="meta">{{ $user->role_label }} · {{ $user->email }}</div>
            @if($user->hasRole('volunteer'))
                <div class="meta">{{ $user->faculty ?: 'Факультет не указан' }}{{ $user->group_name ? ', группа '.$user->group_name : '' }}</div>
            @else
                <div class="meta">{{ $user->organization ?: 'Организация не указана' }}</div>
            @endif
            @if($user->phone)<div class="meta">{{ $user->phone }}</div>@endif
        </div>
        <a class="btn secondary" href="{{ route('profile.edit') }}">Редактировать</a>
    </div>
    @if($user->bio)<p style="margin-bottom:0">{{ $user->bio }}</p>@endif
  </div>

  @if($user->hasRole('volunteer'))
    <div class="stats" style="margin:20px 0">
        <div class="stat"><b>{{ $stats['events'] }}</b><span>мероприятий отработано</span></div>
        <div class="stat"><b>{{ $stats['hours'] }}</b><span>волонтёрских часов</span></div>
        <div class="stat"><b>{{ $stats['points'] }}</b><span>баллов</span></div>
    </div>
    <div class="panel">
        <h3 style="margin-top:0">Мои заявки</h3>
        @if($registrations->isEmpty())<div class="empty">Вы ещё не подавали заявок. <a href="{{ route('events.index') }}">Открыть афишу</a></div>
        @else
        <table><thead><tr><th>Мероприятие</th><th>Дата</th><th>Баллы</th><th>Статус</th></tr></thead><tbody>
        @foreach($registrations as $r)
            <tr><td><a href="{{ route('events.show', $r->event) }}">{{ $r->event->title }}</a></td>
                <td>{{ $r->event->starts_at->translatedFormat('j F Y, H:i') }}</td>
                <td>{{ $r->event->points }}</td>
                <td><span class="badge {{ $r->status }}">{{ $r->status_label }}</span></td></tr>
        @endforeach</tbody></table>
        @endif
    </div>
  @else
    <div class="panel" style="margin-top:20px">
        <div class="head-row"><h3 style="margin:0">Мои мероприятия</h3><a class="btn small" href="{{ route('organizer.events.create') }}">Создать мероприятие</a></div>
        @if($events->isEmpty())<div class="empty">Вы ещё не создали ни одного мероприятия.</div>
        @else
        <table><thead><tr><th>Название</th><th>Дата</th><th>Заявок</th></tr></thead><tbody>
        @foreach($events as $e)
            <tr><td><a href="{{ route('events.show', $e) }}">{{ $e->title }}</a></td><td>{{ $e->starts_at->translatedFormat('j F Y') }}</td><td>{{ $e->registrations->count() }} / {{ $e->capacity }}</td></tr>
        @endforeach</tbody></table>
        @endif
    </div>
  @endif
</div></section>
@endsection
