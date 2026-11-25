<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'counts' => [
                'Пользователи' => User::count(),
                'Мероприятия' => Event::count(),
                'Заявки' => Registration::count(),
                'Статьи' => Article::count(),
            ],
        ]);
    }

    public function index()
    {
        return view('admin.users', ['users' => User::orderBy('role')->orderBy('name')->paginate(20)]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(array_keys(User::ROLES))]]);
        abort_if($user->id === $request->user()->id, 422, 'Нельзя менять собственную роль.');
        $user->update($data);

        return back()->with('status', 'Роль изменена.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Нельзя удалить самого себя.');
        $user->delete();

        return back()->with('status', 'Пользователь удалён.');
    }
}
