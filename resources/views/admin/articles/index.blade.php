@extends('layouts.app')
@section('title', 'Статьи')
@section('content')
<section class="block"><div class="container">
  <p><a href="{{ route('admin.dashboard') }}">← Панель</a></p>
  <div class="head-row"><h2 class="title">Статьи</h2><a class="btn" href="{{ route('admin.articles.create') }}">Новая статья</a></div>
  <div class="panel"><table><thead><tr><th>Заголовок</th><th>Статус</th><th></th></tr></thead><tbody>
  @foreach($articles as $a)
    <tr><td>{{ $a->title }}</td><td>{!! $a->published_at ? '<span class="badge published">Опубликована</span>' : '<span class="badge draft">Черновик</span>' !!}</td>
      <td><div class="actions-cell"><a class="btn small secondary" href="{{ route('admin.articles.edit', $a) }}">Изменить</a>
        <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" data-confirm="Удалить статью?">@csrf @method('DELETE')<button class="btn small danger">Удалить</button></form></div></td></tr>
  @endforeach</tbody></table></div>
  {{ $articles->links() }}
</div></section>
@endsection
