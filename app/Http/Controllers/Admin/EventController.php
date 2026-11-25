<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['organizer', 'category', 'registrations'])->orderByDesc('starts_at')->paginate(20);

        return view('admin.events', compact('events'));
    }
}
