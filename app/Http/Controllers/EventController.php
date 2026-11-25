<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private function filtered(Request $request)
    {
        return Event::published()->upcoming()->with(['category', 'registrations', 'organizer'])
            ->filter($request->only(['q', 'category', 'date_from', 'date_to', 'free']))
            ->orderBy('starts_at');
    }

    public function index(Request $request)
    {
        $events = $this->filtered($request)->paginate(9)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('events.index', compact('events', 'categories'));
    }

    /** Динамический поиск: возвращает JSON для живого списка на странице афиши. */
    public function search(Request $request)
    {
        $events = $this->filtered($request)->take(8)->get()->map(fn (Event $e) => [
            'id' => $e->id,
            'title' => $e->title,
            'category' => $e->category->name,
            'date' => $e->starts_at->translatedFormat('j F, H:i'),
            'location' => $e->location,
            'free' => $e->free_seats,
            'url' => route('events.show', $e),
        ]);

        return response()->json($events);
    }

    public function show(Event $event)
    {
        $user = auth()->user();
        abort_unless($event->status === 'published' || ($user && ($user->hasRole('admin') || $user->id === $event->organizer_id)), 404);

        $event->load(['category', 'organizer', 'registrations']);
        $myRegistration = $user ? $event->registrations->firstWhere('user_id', $user->id) : null;

        return view('events.show', compact('event', 'myRegistration'));
    }
}
