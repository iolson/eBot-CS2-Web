<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Matchs;
use App\Models\Server;
use App\Models\Team;
use App\Services\EbotCommandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Matchs::active()
            ->with(['teamA', 'teamB', 'server', 'event'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.matchs.index', compact('matches'));
    }

    public function archived()
    {
        $matches = Matchs::archived()
            ->with(['teamA', 'teamB', 'event'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.matchs.archived', compact('matches'));
    }

    public function create()
    {
        $events = Event::orderByDesc('id')->get();
        $teams = Team::orderBy('name')->get();
        $servers = Server::orderBy('ip')->get();

        return view('admin.matchs.create', compact('events', 'teams', 'servers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'team_a' => ['required', 'integer', 'exists:teams,id'],
            'team_b' => ['required', 'integer', 'exists:teams,id', 'different:team_a'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'server_id' => ['nullable', 'integer', 'exists:servers,id'],
            'max_round' => ['required', 'integer', 'in:12'],
            'map_selection_mode' => ['required', 'integer'],
        ]);

        $match = Matchs::create($data);

        return redirect()->route('admin.matchs.index')
            ->with('success', __('Match created.'));
    }

    public function show(Matchs $match)
    {
        $match->load(['teamA', 'teamB', 'server', 'event', 'maps.players']);

        return view('admin.matchs.show', compact('match'));
    }

    public function edit(Matchs $match)
    {
        if ($match->isLive()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot edit a match that is currently live.'));
        }

        $events = Event::orderByDesc('id')->get();
        $teams = Team::orderBy('name')->get();
        $servers = Server::orderBy('ip')->get();

        return view('admin.matchs.edit', compact('match', 'events', 'teams', 'servers'));
    }

    public function update(Request $request, Matchs $match): RedirectResponse
    {
        if ($match->isLive()) {
            return redirect()->route('admin.matchs.index')
                ->with('error', __('Cannot edit a match that is currently live.'));
        }

        $data = $request->validate([
            'team_a' => ['required', 'integer', 'exists:teams,id'],
            'team_b' => ['required', 'integer', 'exists:teams,id', 'different:team_a'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'server_id' => ['nullable', 'integer', 'exists:servers,id'],
            'max_round' => ['required', 'integer', 'in:12'],
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
    // Match lifecycle actions
    // -------------------------------------------------------------------------

    public function start(Request $request, Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_NOT_STARTED) {
            return back()->with('error', __('Match cannot be started in its current state.'));
        }

        $server = $this->resolveServer($match, $request->input('server_id'));

        if ($server === null) {
            return back()->with('error', __('No server available. Please assign a server to the match or ensure a free server exists.'));
        }

        $match->update([
            'ip' => $server->ip,
            'server_id' => $server->id,
            'enable' => true,
            'status' => Matchs::STATUS_STARTING,
            'score_a' => 0,
            'score_b' => 0,
            'config_authkey' => $match->config_authkey ?: uniqid(mt_rand(), true),
        ]);

        return back()->with('success', __('Match queued on :server. The eBot daemon will start it shortly.', ['server' => $server->ip]));
    }

    public function startAll(): RedirectResponse
    {
        $pending = Matchs::where('status', Matchs::STATUS_NOT_STARTED)->with('server')->get();
        $started = 0;

        $usedIps = Matchs::live()->pluck('ip')->filter()->all();

        $freeServers = Server::all()->filter(fn ($s) => ! in_array($s->ip, $usedIps, true))->values();
        $serverIndex = 0;

        foreach ($pending as $match) {
            $server = $match->server ?? $freeServers[$serverIndex] ?? null;

            if ($server === null) {
                break;
            }

            if (! $match->server) {
                $serverIndex++;
            }

            $match->update([
                'ip' => $server->ip,
                'server_id' => $server->id,
                'enable' => true,
                'status' => Matchs::STATUS_STARTING,
                'score_a' => 0,
                'score_b' => 0,
                'config_authkey' => $match->config_authkey ?: uniqid(mt_rand(), true),
            ]);

            $started++;
        }

        return back()->with('success', __(':count match(es) queued for start.', ['count' => $started]));
    }

    public function stop(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        app(EbotCommandService::class)->send($match, 'stop');

        return back()->with('info', __('Stop command sent.'));
    }

    public function stopBack(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        app(EbotCommandService::class)->send($match, 'stopback');

        return back()->with('info', __('Stop-back command sent.'));
    }

    public function pauseUnpause(Matchs $match): RedirectResponse
    {
        if (! $match->isLive()) {
            return back()->with('error', __('Match is not currently live.'));
        }

        app(EbotCommandService::class)->send($match, 'pauseunpause');

        return back()->with('info', __('Pause/unpause command sent.'));
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

        app(EbotCommandService::class)->send($match, 'forcestart');

        return back()->with('info', __('Force start command sent.'));
    }

    public function forceKnife(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_WU_KNIFE) {
            return back()->with('error', __('Match is not in knife warmup state.'));
        }

        app(EbotCommandService::class)->send($match, 'forceknife');

        return back()->with('info', __('Force knife command sent.'));
    }

    public function forceKnifeEnd(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_KNIFE) {
            return back()->with('error', __('Knife round is not in progress.'));
        }

        app(EbotCommandService::class)->send($match, 'forceknifeend');

        return back()->with('info', __('Force knife end command sent.'));
    }

    public function passKnife(Matchs $match): RedirectResponse
    {
        if ($match->status !== Matchs::STATUS_KNIFE) {
            return back()->with('error', __('Knife round is not in progress.'));
        }

        app(EbotCommandService::class)->send($match, 'passknife');

        return back()->with('info', __('Pass knife command sent.'));
    }

    /**
     * Resolve which server to use for a match start.
     * Prefers the match's pre-assigned server, then a specific request server_id,
     * then the first server not already hosting a live match.
     */
    private function resolveServer(Matchs $match, ?string $requestServerId): ?Server
    {
        if ($match->server_id) {
            return $match->server;
        }

        if ($requestServerId && is_numeric($requestServerId)) {
            return Server::find($requestServerId);
        }

        $usedIps = Matchs::live()->pluck('ip')->filter()->all();

        return Server::all()->first(fn ($s) => ! in_array($s->ip, $usedIps, true));
    }

    public function reset(Matchs $match): RedirectResponse
    {
        if ($match->enable || $match->isLive()) {
            return back()->with('error', __('Match must be disabled to reset.'));
        }

        $match->update([
            'status' => Matchs::STATUS_NOT_STARTED,
            'score_a' => 0,
            'score_b' => 0,
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
