<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /** Волонтёр подаёт заявку на мероприятие. */
    public function store(Request $request, Event $event)
    {
        $data = $request->validate(['motivation' => 'nullable|string|max:1000']);

        abort_unless($event->status === 'published', 404);

        if ($event->is_over) {
            return back()->with('error', 'Мероприятие уже закончилось.');
        }
        if ($event->registrations()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('error', 'Вы уже подали заявку на это мероприятие.');
        }
        if ($event->load('registrations')->free_seats < 1) {
            return back()->with('error', 'Свободных мест не осталось.');
        }

        $event->registrations()->create([
            'user_id' => $request->user()->id,
            'motivation' => $data['motivation'] ?? null,
        ]);

        return back()->with('status', 'Заявка отправлена. Организатор рассмотрит её в ближайшее время.');
    }

    /** Волонтёр отменяет свою заявку (пока участие не подтверждено). */
    public function destroy(Request $request, Event $event)
    {
        $registration = $event->registrations()->where('user_id', $request->user()->id)->firstOrFail();

        if ($registration->status === 'attended') {
            return back()->with('error', 'Подтверждённое участие отменить нельзя.');
        }
        $registration->delete();

        return back()->with('status', 'Заявка отменена.');
    }
}
