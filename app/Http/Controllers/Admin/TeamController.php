<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
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
        $seasons = Season::where('is_active', true)->orderByDesc('id')->get();

        return view('admin.teams.create', compact('seasons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shorthandle' => ['required', 'string', 'max:10'],
            'flag' => ['nullable', 'string', 'size:2'],
            'link' => ['nullable', 'url', 'max:255'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['integer', 'exists:seasons,id'],
        ]);

        $team = Team::create($data);

        if (! empty($data['seasons'])) {
            $team->seasons()->sync($data['seasons']);
        }

        return redirect()->route('admin.teams.index')
            ->with('success', __('Team created.'));
    }

    public function show(Team $team)
    {
        $team->load('seasons');

        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $team->load('seasons');
        $seasons = Season::orderByDesc('id')->get();

        return view('admin.teams.edit', compact('team', 'seasons'));
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'shorthandle' => ['required', 'string', 'max:10'],
            'flag' => ['nullable', 'string', 'size:2'],
            'link' => ['nullable', 'url', 'max:255'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['integer', 'exists:seasons,id'],
        ]);

        $team->update($data);
        $team->seasons()->sync($data['seasons'] ?? []);

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
     * Return teams for a given season as JSON (used by AJAX match form).
     */
    public function teamsInSeason(Request $request): JsonResponse
    {
        $request->validate([
            'season_id' => ['required', 'integer', 'exists:seasons,id'],
        ]);

        $teams = Team::whereHas('seasons', fn ($q) => $q->where('seasons.id', $request->season_id))
            ->orderBy('name')
            ->get(['id', 'name', 'flag']);

        return response()->json($teams);
    }
}
