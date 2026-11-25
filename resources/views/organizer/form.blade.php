@extends('layouts.app')
@section('title', $event->exists ? 'Редактирование мероприятия' : 'Новое мероприятие')
@section('content')
<div class="container" style="max-width:760px;padding-top:30px">
  <div class="panel">
    <h2 class="title">{{ $event->exists ? 'Редактирование мероприятия' : 'Новое мероприятие' }}</h2>
    <form method="POST" action="{{ $event->exists ? route('organizer.events.update', $event) : route('organizer.events.store') }}">
        @csrf @if($event->exists) @method('PUT') @endif
        <label for="title">Название</label><input id="title" name="title" value="{{ old('title', $event->title) }}" required>
        @error('title')<div class="err">{{ $message }}</div>@enderror
        <div class="row">
            <div><label for="category_id">Категория</label>
                <select id="category_id" name="category_id">@foreach($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $event->category_id) == $c->id)>{{ $c->name }}</option>@endforeach</select>
                @error('category_id')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="status">Статус</label>
                <select id="status" name="status">@foreach(\App\Models\Event::STATUSES as $k => $v)<option value="{{ $k }}" @selected(old('status', $event->status) === $k)>{{ $v }}</option>@endforeach</select></div>
        </div>
        <label for="location">Место проведения</label><input id="location" name="location" value="{{ old('location', $event->location) }}" required>
        @error('location')<div class="err">{{ $message }}</div>@enderror
        <div class="row row3">
            <div><label for="starts_at">Начало</label><input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}" required>
                @error('starts_at')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="ends_at">Окончание</label><input id="ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}" required>
                @error('ends_at')<div class="err">{{ $message }}</div>@enderror</div>
            <div><label for="capacity">Нужно волонтёров</label><input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $event->capacity) }}" required>
                @error('capacity')<div class="err">{{ $message }}</div>@enderror</div>
        </div>
        <label for="description">Описание</label><textarea id="description" name="description" required>{{ old('description', $event->description) }}</textarea>
        @error('description')<div class="err">{{ $message }}</div>@enderror
        <label for="requirements">Требования к волонтёрам</label><textarea id="requirements" name="requirements">{{ old('requirements', $event->requirements) }}</textarea>
        <p><button class="btn">Сохранить</button> <a class="btn secondary" href="{{ route('organizer.events') }}">Отмена</a></p>
    </form>
  </div>
</div>
@endsection
