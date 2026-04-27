<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchs;
use App\Models\Season;
use App\Models\Server;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Matchs::active()
            ->with(['teamA', 'teamB', 'server', 'season'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.matchs.index', compact('matches'));
    }

    public function archived()
    {
        $matches = Matchs::archived()
            ->with(['teamA', 'teamB', 'season'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.matchs.archived', compact('matches'));
    }

    public function create()
    {
        $seasons = Season::orderByDesc('id')->get();
        $teams   = Team::orderBy('name')->get();
        $servers = Server::orderBy('ip')->get();

        return view('admin.matchs.create', compact('seasons', 'teams', 'servers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'team_a'             => ['required', 'integer', 'exists:teams,id'],
            'team_b'             => ['required', 'integer', 'exists:teams,id', 'different:team_a'],
            'season_id'          => ['nullable', 'integer', 'exists:seasons,id'],
            'server_id'          => ['nullable', 'integer', 'exists:servers,id'],
            'max_round'          => ['required', 'integer', 'in:15,25,30'],
            'map_selection_mode' => ['required', 'integer'],
        ]);

        $match = Matchs::create($data);

        return redirect()->route('admin.matchs.index')
            ->with('success', __('Match created.'));
    }

    public function show(Matchs $match)
    {
        $match->load(['teamA', 'teamB', 'server', 'season', 'maps.players']);

        return view('admin.matchs.show', compact('match'));
    }

    public function edit(Matchs $match)
    {
        if ($match->isLive()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot edit a match that is currently live.'));
        }

        $seasons = Season::orderByDesc('id')->get();
        $teams   = Team::orderBy('name')->get();
        $servers = Server::orderBy('ip')->get();

        return view('admin.matchs.edit', compact('match', 'seasons', 'teams', 'servers'));
    }

    public function update(Request $request, Matchs $match): RedirectResponse
    {
        if ($match->isLive()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot edit a match that is currently live.'));
        }

        $data = $request->validate([
            'team_a'             => ['required', 'integer', 'exists:teams,id'],
            'team_b'             => ['required', 'integer', 'exists:teams,id', 'different:team_a'],
            'season_id'          => ['nullable', 'integer', 'exists:seasons,id'],
            'server_id'          => ['nullable', 'integer', 'exists:servers,id'],
            'max_round'          => ['required', 'integer', 'in:15,25,30'],
            'map_selection_mode' => ['required', 'integer'],
        ]);

        $match->update($data);

        return redirect()->route('admin.matchs.index')
            ->with('success', __('Match updated.'));
    }

    public function destroy(Matchs $match): RedirectResponse
    {
        if ($match->isLive()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot delete a match that is currently live.'));
        }

        if ($match->isArchived()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot delete an archived match.'));
        }

        $match->delete();

        return redirect()->route('admin.matchs.index')
            ->with('success', __('Match deleted.'));
    }

    public function duplicate(Matchs $match): RedirectResponse
    {
        $clone = $match->replicate(['status', 'enable']);
        $clone->status = Matchs::STATUS_NOT_STARTED;
        $clone->enable = 0;
        $clone->save();

        return redirect()->route('admin.matchs.edit', $clone)
            ->with('success', __('Match duplicated.'));
    }

    // -------------------------------------------------------------------------
    // Match lifecycle actions (server communication wired up in Phase 8)
    // -------------------------------------------------------------------------

    public function start(Request $request, Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_NOT_STARTED) {
            return back()->with('error', __('Match cannot be started in its current state.'));
        }

        // Phase 8 will send the encrypted command to the game server.
        return back()->with('info', __('Start command queued (server integration pending).'));
    }

    public function startAll(): RedirectResponse
    {
        // Phase 8 will iterate unstarted matches and dispatch start commands.
        return back()->with('info', __('Start all command queued (server integration pending).'));
    }

    public function stop(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        return back()->with('info', __('Stop command queued (server integration pending).'));
    }

    public function stopBack(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        return back()->with('info', __('Stop-back command queued (server integration pending).'));
    }

    public function pauseUnpause(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        return back()->with('info', __('Pause/unpause command queued (server integration pending).'));
    }

    public function forceStart(Matchs $match): RedirectResponse
    {
        $warmupStatuses = [
            Matchs::STATUS_WU_1_SIDE,
            Matchs::STATUS_WU_2_SIDE,
            Matchs::STATUS_WU_KNIFE,
            Matchs::STATUS_FIRST_SIDE,
            Matchs::STATUS_SECOND_SIDE,
        ];

        if (! in_array($match->status, $warmupStatuses, true)) {
            return back()->with('error', __('Match is not in a warmup or halftime state.'));
        }

        return back()->with('info', __('Force start command queued (server integration pending).'));
    }

    public function forceKnife(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_WU_KNIFE) {
            return back()->with('error', __('Match is not in knife warmup state.'));
        }

        return back()->with('info', __('Force knife command queued (server integration pending).'));
    }

    public function forceKnifeEnd(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_KNIFE) {
            return back()->with('error', __('Knife round is not in progress.'));
        }

        return back()->with('info', __('Force knife end command queued (server integration pending).'));
    }

    public function passKnife(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_KNIFE) {
            return back()->with('error', __('Knife round is not in progress.'));
        }

        return back()->with('info', __('Pass knife command queued (server integration pending).'));
    }

    public function reset(Matchs $match): RedirectResponse
    {
        if ($match->enable || $match->isLive()) {
            return back()->with('error', __('Match must be disabled to reset.'));
        }

        $match->update([
            'status'    => Matchs::STATUS_NOT_STARTED,
            'score_a'   => 0,
            'score_b'   => 0,
        ]);

        return back()->with('success', __('Match reset.'));
    }

    public function setArchive(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_END_MATCH) {
            return back()->with('error', __('Match must be finished before archiving.'));
        }

        $match->update(['status' => Matchs::STATUS_ARCHIVE]);

        return back()->with('success', __('Match archived.'));
    }

    public function archiveAll(): RedirectResponse
    {
        Matchs::where('status', Matchs::STATUS_END_MATCH)->update(['status' => Matchs::STATUS_ARCHIVE]);

        return back()->with('success', __('All finished matches archived.'));
    }

    public function editScore(Request $request, Matchs $match): RedirectResponse
    {
        $data = $request->validate([
            'score_a' => ['required', 'integer', 'min:0'],
            'score_b' => ['required', 'integer', 'min:0'],
        ]);

        $match->update($data);

        return back()->with('success', __('Score updated.'));
    }
}
