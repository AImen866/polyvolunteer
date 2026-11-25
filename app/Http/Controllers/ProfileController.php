<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $data = ['user' => $user];

        if ($user->hasRole('volunteer')) {
            $data['registrations'] = $user->registrations()->with('event.category')->latest()->get();
            $data['stats'] = $user->volunteerStats();
        } else {
            $data['events'] = $user->organizedEvents()->with('registrations')->get();
        }

        return view('profile.show', $data);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,'.$user->id,
            'phone' => ['nullable', 'regex:/^\+?[0-9\s\-()]{10,20}$/'],
            'faculty' => 'nullable|string|max:150',
            'group_name' => 'nullable|string|max:30',
            'organization' => 'nullable|string|max:150',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('profile.show')->with('status', 'Профиль обновлён.');
    }
}
