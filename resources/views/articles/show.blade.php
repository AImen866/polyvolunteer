@extends('layouts.app')
@section('title', $article->title)
@section('content')
<section class="block"><div class="container" style="max-width:780px">
  <div class="panel">
    <h1 style="color:var(--navy);margin-top:0">{{ $article->title }}</h1>
    <div class="meta">{{ $article->published_at->translatedFormat('j F Y') }} · {{ $article->author->name }}</div>
    <div class="article-body" style="margin-top:18px">
        @foreach(preg_split('/\R{2,}/', $article->body) as $p)<p>{{ $p }}</p>@endforeach
    </div>
  </div>
  <h3>Читайте также</h3>
  <ul>@foreach($others as $o)<li><a href="{{ route('articles.show', $o) }}">{{ $o->title }}</a></li>@endforeach</ul>
</div></section>
@endsection
