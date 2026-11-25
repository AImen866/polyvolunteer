@extends('layouts.app')
@section('title', 'Афиша')
@section('content')
<section class="block"><div class="container">
    <h2 class="title">Афиша мероприятий</h2>
    <form id="event-filters" class="panel filters" method="GET" action="{{ route('events.index') }}" data-search="{{ route('events.search') }}">
        <div><label for="q">Поиск</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Название, место, описание"></div>
        <div><label for="category">Категория</label>
            <select id="category" name="category"><option value="">Все</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
            </select></div>
        <div><label for="date_from">С даты</label><input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"></div>
        <div><label for="date_to">По дату</label><input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"></div>
        <div><label class="check"><input type="checkbox" name="free" value="1" @checked(request('free'))> Есть места</label>
            <button class="btn small" style="margin-top:6px">Найти</button></div>
    </form>
    <ul id="live-results" class="live-list hide"></ul>

    @if($events->isEmpty())<div class="panel empty">По вашему запросу ничего не найдено.</div>
    @else<div class="grid">@foreach($events as $event)@include('partials.event-card')@endforeach</div>
    {{ $events->links() }}@endif
</div></section>
@endsection
