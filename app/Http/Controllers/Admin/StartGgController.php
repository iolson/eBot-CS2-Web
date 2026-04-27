<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchs;
use App\Services\StartGgService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartGgController extends Controller
{
    public function __construct(private readonly StartGgService $startGg) {}

    /**
     * Three-level drill-down: tournaments → events → sets.
     */
    public function index(Request $request)
    {
        if (! $this->startGg->isConfigured()) {
            return view('admin.startgg.index', [
                'configured' => false,
                'tournaments' => [],
                'events' => [],
                'sets' => [],
                'tournamentSlug' => null,
                'eventId' => null,
            ]);
        }

        $tournamentSlug = $request->query('tournament');
        $eventId = $request->query('event') ? (int) $request->query('event') : null;
        $tournaments = [];
        $events = [];
        $sets = [];

        try {
            $tournaments = $this->startGg->getTournamentsByOwner();

            if ($tournamentSlug) {
                $events = $this->startGg->getTournamentEvents($tournamentSlug);
            }

            if ($eventId) {
                $sets = $this->startGg->getEventSets($eventId);
            }
        } catch (\Throwable $e) {
            return view('admin.startgg.index', [
                'configured' => true,
                'tournaments' => [],
                'events' => [],
                'sets' => [],
                'tournamentSlug' => $tournamentSlug,
                'eventId' => $eventId,
                'apiError' => $e->getMessage(),
            ]);
        }

        return view('admin.startgg.index', compact('tournaments', 'events', 'sets', 'tournamentSlug', 'eventId') + ['configured' => true]);
    }

    /**
     * Import a single set from start.gg as a new eBot match.
     */
    public function import(Request $request): JsonResponse
    {
        if (! $this->startGg->isConfigured()) {
            return response()->json(['status' => false, 'error' => 'not_configured']);
        }

        $eventId = $request->input('eventId');
        $setId = $request->input('setId');

        if (! $eventId || ! $setId) {
            return response()->json(['status' => false, 'error' => 'missing_params']);
        }

        try {
            $set = $this->startGg->getSet((int) $setId);

            $identifier = "startgg.{$eventId}.{$setId}";

            // Return existing match if already imported
            $existing = Matchs::where('identifier_id', $identifier)->first();
            if ($existing) {
                return response()->json(['status' => false, 'matchId' => $existing->id]);
            }

            $teamA = $set['slots'][0]['entrant']['name'] ?? 'Team A';
            $teamB = $set['slots'][1]['entrant']['name'] ?? 'Team B';

            $match = Matchs::create([
                'team_a_name' => $teamA,
                'team_b_name' => $teamB,
                'team_a_flag' => null,
                'team_b_flag' => null,
                'max_round' => config('ebot.default_max_round', 12),
                'overtime_startmoney' => config('ebot.default_overtime_startmoney', 10000),
                'overtime_max_round' => config('ebot.default_overtime_max_round', 3),
                'config_ot' => true,
                'config_full_score' => false,
                'config_streamer' => false,
                'config_knife_round' => true,
                'map_selection_mode' => Matchs::MAP_SELECTION_NORMAL,
                'score_a' => 0,
                'score_b' => 0,
                'status' => Matchs::STATUS_NOT_STARTED,
                'config_authkey' => uniqid(mt_rand(), true),
                'identifier_id' => $identifier,
                'enable' => false,
            ]);

            return response()->json(['status' => true, 'matchId' => $match->id]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'error' => 'api_error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Export match results back to start.gg via reportBracketSet mutation.
     */
    public function export(Matchs $match): RedirectResponse
    {
        if (! $this->startGg->isConfigured()) {
            return back()->with('error', __('start.gg is not configured.'));
        }

        if (! $match->identifier_id || ! str_starts_with($match->identifier_id, 'startgg.')) {
            return back()->with('error', __('This match is not linked to a start.gg set.'));
        }

        $parts = explode('.', $match->identifier_id);
        if (count($parts) < 3) {
            return back()->with('error', __('Invalid start.gg identifier.'));
        }

        $setId = (int) $parts[2];

        try {
            $set = $this->startGg->getSet($setId);
            $slots = $set['slots'] ?? [];

            // Determine winner entrant ID from match scores
            $winnerId = null;
            if ($match->status >= Matchs::STATUS_END_MATCH) {
                if ($match->score_a > $match->score_b && isset($slots[0]['entrant']['id'])) {
                    $winnerId = (int) $slots[0]['entrant']['id'];
                } elseif ($match->score_b > $match->score_a && isset($slots[1]['entrant']['id'])) {
                    $winnerId = (int) $slots[1]['entrant']['id'];
                }
            }

            if (! $winnerId) {
                return back()->with('error', __('Cannot export: match has no winner yet.'));
            }

            // Build game data from maps
            $match->load('maps');
            $gameData = [];
            foreach ($match->maps as $index => $map) {
                $gameData[] = [
                    'gameNum' => $index + 1,
                    'winnerId' => $map->score_a > $map->score_b ? (int) $slots[0]['entrant']['id'] : (int) $slots[1]['entrant']['id'],
                ];
            }

            $this->startGg->reportSet($setId, $winnerId, $gameData);

            return back()->with('success', __('Match results exported to start.gg.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('start.gg export failed: :msg', ['msg' => $e->getMessage()]));
        }
    }
}
