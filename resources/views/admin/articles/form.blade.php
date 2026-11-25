@extends('layouts.app')
@section('title', 'Статья')
@section('content')
<div class="container" style="max-width:780px;padding-top:30px"><div class="panel">
  <h2 class="title">{{ $article->exists ? 'Редактирование статьи' : 'Новая статья' }}</h2>
  <form method="POST" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
    @csrf @if($article->exists) @method('PUT') @endif
    <label for="title">Заголовок</label><input id="title" name="title" value="{{ old('title', $article->title) }}" required>
    @error('title')<div class="err">{{ $message }}</div>@enderror
    <label for="excerpt">Краткое описание</label><input id="excerpt" name="excerpt" value="{{ old('excerpt', $article->excerpt) }}" required>
    @error('excerpt')<div class="err">{{ $message }}</div>@enderror
    <label for="body">Текст (абзацы разделяйте пустой строкой)</label><textarea id="body" name="body" style="min-height:260px" required>{{ old('body', $article->body) }}</textarea>
    @error('body')<div class="err">{{ $message }}</div>@enderror
    <label class="check"><input type="checkbox" name="publish" value="1" @checked(old('publish', $article->published_at))> Опубликовать</label>
    <p><button class="btn">Сохранить</button> <a class="btn secondary" href="{{ route('admin.articles.index') }}">Отмена</a></p>
  </form></div></div>
@endsection
