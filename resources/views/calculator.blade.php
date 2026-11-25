@extends('layouts.app')
@section('title', 'Калькулятор волонтёров')
@section('content')
<section class="block"><div class="container" style="max-width:760px">
  <div class="panel">
    <h2 class="title">Калькулятор числа волонтёров</h2>
    <p class="muted">Организаторам: рассчитайте, сколько волонтёров нужно для мероприятия. Волонтёрам: узнайте, сколько баллов можно получить за смену.</p>
    <form id="calc-form" method="POST" action="{{ route('calculator.calculate') }}">@csrf
        <label for="category_id">Тип мероприятия</label>
        <select id="category_id" name="category_id">
            @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }} (1 волонтёр на {{ $c->guests_per_volunteer }} гостей, коэфф. {{ $c->hours_coefficient }})</option>@endforeach
        </select>
        <div class="row">
            <div><label for="guests">Ожидаемое число гостей</label><input id="guests" name="guests" type="number" min="1" value="200"></div>
            <div><label for="hours">Длительность, часов</label><input id="hours" name="hours" type="number" min="0.5" step="0.5" value="8"></div>
        </div>
        <p><button class="btn">Рассчитать</button></p>
    </form>
    <div id="calc-result" class="result hide">
        <div class="muted">Нужно волонтёров всего</div>
        <div class="big" data-total>0</div>
        <p data-detail></p>
        <p class="muted" style="margin:0">Формула: (⌈гости ÷ норма⌉ + резерв 15%) × ⌈часы ÷ 6⌉</p>
    </div>
  </div>
</div></section>
@endsection
