@extends('layouts.app')
@section('title', 'Пользователи')
@section('content')
<section class="block"><div class="container">
  <p><a href="{{ route('admin.dashboard') }}">← Панель</a></p>
  <h2 class="title">Пользователи</h2>
  <div class="panel"><table><thead><tr><th>ФИО</th><th>Почта</th><th>Роль</th><th></th></tr></thead><tbody>
  @foreach($users as $u)
    <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td>
      <td>@if($u->id === auth()->id()){{ $u->role_label }}@else
        <form method="POST" action="{{ route('admin.users.update', $u) }}" style="display:flex;gap:6px">@csrf @method('PATCH')
          <select name="role" style="width:auto">@foreach(\App\Models\User::ROLES as $k => $v)<option value="{{ $k }}" @selected($u->role === $k)>{{ $v }}</option>@endforeach</select>
          <button class="btn small secondary">Сохранить</button></form>@endif</td>
      <td>@if($u->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Удалить пользователя?">@csrf @method('DELETE')<button class="btn small danger">Удалить</button></form>@endif</td></tr>
  @endforeach</tbody></table></div>
  {{ $users->links() }}
</div></section>
@endsection
