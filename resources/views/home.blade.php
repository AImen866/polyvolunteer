@extends('layouts.app')
@section('title', 'Главная')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Афиша волонтёрских мероприятий Московского Политеха</h1>
        <p>Выбирайте события университета, подавайте заявки в один клик и копите волонтёрские часы. Организаторы публикуют мероприятия и набирают команду.</p>
        <div class="actions">
            <a class="btn" href="{{ route('events.index') }}">Смотреть афишу</a>
            @guest<a class="btn secondary" href="{{ route('register') }}">Стать волонтёром</a>@endguest
        </div>
    </div>
</section>
<div class="container">
    <div class="stats">
        <div class="stat"><b>{{ $stats['volunteers'] }}</b><span>волонтёров в базе</span></div>
        <div class="stat"><b>{{ $stats['events'] }}</b><span>опубликованных мероприятий</span></div>
        <div class="stat"><b>{{ $stats['hours'] }}</b><span>часов отработано</span></div>
    </div>
</div>
<section class="block"><div class="container">
    <div class="head-row"><h2 class="title">Ближайшие мероприятия</h2><a href="{{ route('events.index') }}">Вся афиша</a></div>
    @if($events->isEmpty())<div class="panel empty">Пока нет предстоящих мероприятий.</div>
    @else<div class="grid">@foreach($events as $event)@include('partials.event-card')@endforeach</div>@endif
</div></section>
<section class="block" style="padding-top:0"><div class="container">
    <div class="head-row"><h2 class="title">Полезные статьи</h2><a href="{{ route('articles.index') }}">Все статьи</a></div>
    <div class="grid">
        @foreach($articles as $a)
        <article class="card"><div class="body">
            <h3><a href="{{ route('articles.show', $a) }}">{{ $a->title }}</a></h3>
            <div class="meta">{{ $a->published_at->translatedFormat('j F Y') }}</div>
            <p style="margin:0">{{ $a->excerpt }}</p>
        </div></article>
        @endforeach
    </div>
</div></section>
@endsection
