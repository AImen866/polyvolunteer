<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:5000',
            'requirements' => 'nullable|string|max:2000',
            'location' => 'required|string|max:200',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'capacity' => 'required|integer|min:1|max:1000',
            'status' => ['required', Rule::in(array_keys(Event::STATUSES))],
        ];
    }

    private function own(Event $event): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin') || $event->organizer_id === $user->id, 403);
    }

    public function index(Request $request)
    {
        $events = $request->user()->organizedEvents()->with(['category', 'registrations'])
            ->orderByDesc('starts_at')->get();

        return view('organizer.index', compact('events'));
    }

    public function create()
    {
        return view('organizer.form', [
            'event' => new Event(['capacity' => 10, 'status' => 'published']),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $event = $request->user()->organizedEvents()->create($request->validate($this->rules()));

        return redirect()->route('events.show', $event)->with('status', 'Мероприятие создано.');
    }

    public function edit(Event $event)
    {
        $this->own($event);

        return view('organizer.form', ['event' => $event, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Event $event)
    {
        $this->own($event);
        $event->update($request->validate($this->rules()));

        return redirect()->route('events.show', $event)->with('status', 'Изменения сохранены.');
    }

    public function destroy(Event $event)
    {
        $this->own($event);
        $event->delete();

        return redirect()->route('organizer.events')->with('status', 'Мероприятие удалено.');
    }

    /** Список заявок на мероприятие. */
    public function applications(Event $event)
    {
        $this->own($event);
        $event->load(['registrations.user', 'category']);

        return view('organizer.applications', compact('event'));
    }

    /** Смена статуса заявки: одобрить, отклонить, подтвердить участие. */
    public function updateRegistration(Request $request, Registration $registration)
    {
        $this->own($registration->event);
        $data = $request->validate(['status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'attended'])]]);

        if ($data['status'] === 'attended' && ! $registration->event->starts_at->isPast()) {
            return back()->with('error', 'Участие можно подтвердить только после начала мероприятия.');
        }
        $registration->update($data);

        return back()->with('status', 'Статус заявки обновлён.');
    }
}
