@extends('layouts.app')
@section('title', $event->title)
@section('content')
<section class="block"><div class="container detail">
  <div>
    <div class="panel">
        <span class="tag plain">{{ $event->category->name }}</span>
        <span class="badge {{ $event->status }}">{{ \App\Models\Event::STATUSES[$event->status] }}</span>
        <h1 style="margin:12px 0 6px;color:var(--navy)">{{ $event->title }}</h1>
        <div class="meta">Организатор: {{ $event->organizer->organization ?: $event->organizer->name }}</div>
        @unless($event->is_over)
        <div id="countdown" class="countdown" data-start="{{ $event->starts_at->toIso8601String() }}">
            <div><b data-d>0</b><small>дней</small></div><div><b data-h>00</b><small>часов</small></div>
            <div><b data-m>00</b><small>минут</small></div><div><b data-s>00</b><small>секунд</small></div>
        </div>
        @endunless
        <h3>Описание</h3>
        <p style="white-space:pre-line">{{ $event->description }}</p>
        @if($event->requirements)<h3>Требования к волонтёрам</h3><p style="white-space:pre-line">{{ $event->requirements }}</p>@endif
    </div>

    @auth
    @if(auth()->user()->hasRole('volunteer') && $event->status === 'published')
    <div class="panel">
        @if($myRegistration)
            <h3 style="margin-top:0">Ваша заявка: <span class="badge {{ $myRegistration->status }}">{{ $myRegistration->status_label }}</span></h3>
            @if($myRegistration->status !== 'attended')
            <form method="POST" action="{{ route('registrations.destroy', $event) }}" data-confirm="Отменить заявку?">@csrf @method('DELETE')
                <button class="btn secondary">Отменить заявку</button></form>
            @endif
        @elseif($event->is_over)
            <p class="muted">Мероприятие уже прошло.</p>
        @elseif($event->free_seats < 1)
            <p class="muted">Свободных мест не осталось.</p>
        @else
            <h3 style="margin-top:0">Записаться на мероприятие</h3>
            <form method="POST" action="{{ route('registrations.store', $event) }}">@csrf
                <label for="motivation">Почему хотите участвовать (необязательно)</label>
                <textarea id="motivation" name="motivation">{{ old('motivation') }}</textarea>
                @error('motivation')<div class="err">{{ $message }}</div>@enderror
                <p><button class="btn">Отправить заявку</button></p>
            </form>
        @endif
    </div>
    @endif
    @else
    <div class="panel"><a href="{{ route('login') }}">Войдите</a> или <a href="{{ route('register') }}">зарегистрируйтесь</a>, чтобы подать заявку.</div>
    @endauth
  </div>

  <aside class="panel">
    <ul class="facts">
        <li><span>Начало</span><b>{{ $event->starts_at->translatedFormat('j F Y, H:i') }}</b></li>
        <li><span>Окончание</span><b>{{ $event->ends_at->translatedFormat('j F, H:i') }}</b></li>
        <li><span>Место</span><b>{{ $event->location }}</b></li>
        <li><span>Длительность</span><b>{{ $event->hours }} ч</b></li>
        <li><span>Мест всего</span><b>{{ $event->capacity }}</b></li>
        <li><span>Свободно</span><b>{{ $event->free_seats }}</b></li>
        <li><span>Баллы волонтёру</span><b>{{ $event->points }}</b></li>
    </ul>
    <div class="progress {{ $event->free_seats === 0 ? 'full' : '' }}" style="margin-top:14px"><i style="width: {{ $event->fill_percent }}%"></i></div>
    @auth @if(auth()->user()->hasRole('admin') || auth()->id() === $event->organizer_id)
        <p style="margin-top:18px;display:flex;gap:8px;flex-wrap:wrap">
            <a class="btn small" href="{{ route('organizer.applications', $event) }}">Заявки</a>
            <a class="btn small secondary" href="{{ route('organizer.events.edit', $event) }}">Изменить</a></p>
    @endif @endauth
  </aside>
</div></section>
@endsection
