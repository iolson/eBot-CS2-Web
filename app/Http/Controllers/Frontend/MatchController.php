<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GameMap;
use App\Models\Matchs;
use App\Models\PlayerHeatmap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MatchController extends Controller
{
    public function index()
    {
        $matches = Matchs::active()
            ->with(['teamA', 'teamB', 'server', 'event'])
            ->orderByDesc('id')
            ->paginate(12);

        return view('matchs.index', compact('matches'));
    }

    public function archived()
    {
        $matches = Matchs::archived()
            ->with(['teamA', 'teamB', 'event'])
            ->orderByDesc('id')
            ->paginate(12);

        return view('matchs.archived', compact('matches'));
    }

    public function show(Matchs $match)
    {
        $match->load(['teamA', 'teamB', 'server', 'event', 'maps.players']);

        $hasHeatmap = PlayerHeatmap::where('match_id', $match->id)->exists();

        return view('matchs.show', compact('match', 'hasHeatmap'));
    }

    /**
     * Serve a demo file (.dem.zip) for a map as a download.
     */
    public function demo(GameMap $map): BinaryFileResponse
    {
        if (config('ebot.demo_download') === false) {
            abort(403, 'Demo downloads are disabled.');
        }

        $file = $map->tv_record_file;

        if (! $file || str_contains($file, '/') || str_contains($file, '..')) {
            abort(404, 'Demo file not available.');
        }

        $path = rtrim(config('ebot.demo_path'), '/').'/'.$file.'.dem.zip';

        if (! file_exists($path)) {
            abort(404, 'Demo file not found.');
        }

        return response()->download($path, $file.'.dem.zip');
    }

    /**
     * Return heatmap data for a match as JSON.
     */
    public function heatmapData(Request $request, Matchs $match): JsonResponse
    {
        $request->validate([
            'type' => ['nullable', 'string', 'in:kill,grenade'],
            'side' => ['nullable', 'string', 'in:ct,t,all'],
            'map_id' => ['nullable', 'integer'],
        ]);

        $query = PlayerHeatmap::where('match_id', $match->id);

        if ($request->filled('map_id')) {
            $query->where('map_id', $request->map_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('side') && $request->side !== 'all') {
            $query->where('side', $request->side);
        }

        $data = $query->get(['x', 'y', 'z', 'type', 'side'])->toArray();

        return response()->json($data);
    }

    /**
     * Serve the match log file as plain text.
     */
    public function logs(Matchs $match)
    {
        $logPath = storage_path("app/logs/match_{$match->id}.html");

        if (! file_exists($logPath)) {
            abort(404, 'Log file not found.');
        }

        return response()->file($logPath, ['Content-Type' => 'text/html']);
    }

    /**
     * Export player statistics for a match as JSON.
     */
    public function exportPlayers(Matchs $match): JsonResponse
    {
        $match->load(['maps.players']);

        $players = [];
        foreach ($match->maps as $map) {
            foreach ($map->players as $player) {
                $players[] = [
                    'steamid' => $player->steamid,
                    'name' => $player->pseudo,
                    'team' => $player->team,
                    'kills' => $player->nb_kill,
                    'deaths' => $player->death,
                    'assists' => $player->assist,
                    'hs' => $player->hs,
                    'kd_ratio' => $player->getKdRatio(),
                    'hs_pct' => $player->getHsPercent(),
                ];
            }
        }

        return response()->json(['match_id' => $match->id, 'players' => $players]);
    }

    /**
     * Export round-by-round data for a match as JSON.
     */
    public function exportRounds(Matchs $match): JsonResponse
    {
        $match->load(['maps.roundSummaries']);

        $rounds = [];
        foreach ($match->maps as $map) {
            foreach ($map->roundSummaries as $summary) {
                $rounds[] = [
                    'map_id' => $map->id,
                    'team' => $summary->team,
                    'win_type' => $summary->win_type,
                ];
            }
        }

        return response()->json(['match_id' => $match->id, 'rounds' => $rounds]);
    }

    /**
     * Export all kill events for a match as JSON.
     */
    public function exportKills(Matchs $match): JsonResponse
    {
        $match->load('maps.playerKills');

        $kills = [];
        foreach ($match->maps as $map) {
            foreach ($map->playerKills as $kill) {
                $kills[] = [
                    'map_id' => $map->id,
                    'killer' => $kill->killer_id,
                    'killed' => $kill->killed_id,
                    'weapon' => $kill->weapon,
                    'headshot' => $kill->headshot,
                    'killer_team' => $kill->killer_team,
                    'killed_team' => $kill->killed_team,
                ];
            }
        }

        return response()->json(['match_id' => $match->id, 'kills' => $kills]);
    }

    /**
     * Full match statistics export (eStats-compatible format).
     */
    public function exportEstats(Matchs $match): JsonResponse
    {
        $match->load([
            'teamA',
            'teamB',
            'event',
            'server',
            'maps.players',
            'maps.rounds.roundSummaries',
            'maps.playerKills',
        ]);

        return response()->json([
            'match' => [
                'id' => $match->id,
                'team_a' => $match->teamA?->name,
                'team_b' => $match->teamB?->name,
                'score_a' => $match->score_a,
                'score_b' => $match->score_b,
                'status' => $match->getStatusText(),
                'event' => $match->event?->name,
            ],
            'maps' => $match->maps->map(fn ($map) => [
                'id' => $map->id,
                'map' => $map->map_name,
                'score_1' => $map->score_1,
                'score_2' => $map->score_2,
                'players' => $map->players->map(fn ($p) => [
                    'steamid' => $p->steamid,
                    'name' => $p->pseudo,
                    'team' => $p->team,
                    'kills' => $p->nb_kill,
                    'deaths' => $p->death,
                    'assists' => $p->assist,
                    'hs' => $p->hs,
                ])->toArray(),
            ])->toArray(),
        ]);
    }
}
