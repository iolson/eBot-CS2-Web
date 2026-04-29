<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $activeEvents = Event::active()->orderByDesc('start')->get();
        $pastEvents = Event::where('active', false)->orderByDesc('start')->limit(5)->get();

        return view('events.index', compact('activeEvents', 'pastEvents'));
    }

    public function select(Request $request, Event $event): RedirectResponse
    {
        session()->put('selected_event_id', $event->id);

        $redirect = $request->input('site', 'matchs');

        return match ($redirect) {
            'archived' => redirect()->route('matchs.archived'),
            default => redirect()->route('matchs.index'),
        };
    }
}
