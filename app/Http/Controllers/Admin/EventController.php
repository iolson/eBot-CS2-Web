<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('id')->paginate(25);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'url', 'max:255'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'active' => ['boolean'],
        ]);

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', __('Event created.'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'url', 'max:255'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'active' => ['boolean'],
        ]);

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', __('Event updated.'));
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', __('Event deleted.'));
    }

    public function deactivate(Event $event): RedirectResponse
    {
        $event->update(['active' => ! $event->active]);

        $message = $event->active ? __('Event activated.') : __('Event deactivated.');

        return back()->with('success', $message);
    }
}
