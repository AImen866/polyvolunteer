<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::published()->upcoming()->with(['category', 'registrations'])
            ->orderBy('starts_at')->take(6)->get();

        $stats = [
            'volunteers' => User::where('role', 'volunteer')->count(),
            'events' => Event::published()->count(),
            'hours' => round(Registration::where('status', 'attended')->with('event')->get()->sum(fn ($r) => $r->event->hours)),
        ];

        $articles = Article::whereNotNull('published_at')->latest('published_at')->take(3)->get();

        return view('home', compact('events', 'stats', 'articles'));
    }
}
