<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::orderBy('name')->paginate(25);

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $events = Event::where('is_active', true)->orderByDesc('id')->get();

        return view('admin.teams.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shorthandle' => ['required', 'string', 'max:10'],
            'flag' => ['nullable', 'string', 'size:2'],
            'link' => ['nullable', 'url', 'max:255'],
            'events' => ['nullable', 'array'],
            'events.*' => ['integer', 'exists:events,id'],
        ]);

        $team = Team::create($data);

        if (! empty($data['events'])) {
            $team->events()->sync($data['events']);
        }

        return redirect()->route('admin.teams.index')
            ->with('success', __('Team created.'));
    }

    public function show(Team $team)
    {
        $team->load('events');

        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $team->load('events');
        $events = Event::orderByDesc('id')->get();

        return view('admin.teams.edit', compact('team', 'events'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shorthandle' => ['required', 'string', 'max:10'],
            'flag' => ['nullable', 'string', 'size:2'],
            'link' => ['nullable', 'url', 'max:255'],
            'events' => ['nullable', 'array'],
            'events.*' => ['integer', 'exists:events,id'],
        ]);

        $team->update($data);
        $team->events()->sync($data['events'] ?? []);

        return redirect()->route('admin.teams.index')
            ->with('success', __('Team updated.'));
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', __('Team deleted.'));
    }

    /**
     * Return teams for a given event as JSON (used by AJAX match form).
     */
    public function teamsInEvent(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $teams = Team::whereHas('events', fn ($q) => $q->where('events.id', $request->event_id))
            ->orderBy('name')
            ->get(['id', 'name', 'flag']);

        return response()->json($teams);
    }
}
