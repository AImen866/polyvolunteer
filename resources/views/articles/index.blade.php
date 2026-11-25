@extends('layouts.app')
@section('title', 'Статьи')
@section('content')
<section class="block"><div class="container">
    <h2 class="title">Статьи для волонтёров</h2>
    <div class="grid">
    @foreach($articles as $a)
        <article class="card"><div class="body">
            <h3><a href="{{ route('articles.show', $a) }}">{{ $a->title }}</a></h3>
            <div class="meta">{{ $a->published_at->translatedFormat('j F Y') }} · {{ $a->author->name }}</div>
            <p style="margin:0">{{ $a->excerpt }}</p>
            <div class="foot"><a href="{{ route('articles.show', $a) }}">Читать дальше</a></div>
        </div></article>
    @endforeach
    </div>
    {{ $articles->links() }}
</div></section>
@endsection
