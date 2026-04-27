<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchs;
use App\Services\ToornamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ToornamentController extends Controller
{
    public function __construct(private readonly ToornamentService $toornament) {}

    /**
     * Browse Toornament tournaments and, optionally, matches within one tournament.
     */
    public function index(Request $request)
    {
        if (! $this->toornament->isConfigured()) {
            return view('admin.toornament.index', [
                'configured' => false,
                'tournaments' => [],
                'matches' => [],
                'tournamentId' => null,
            ]);
        }

        $tournamentId = $request->query('id');
        $tournaments = [];
        $matches = [];

        try {
            $tournaments = $this->toornament->getTournaments();

            if ($tournamentId) {
                $matches = $this->toornament->getTournamentMatches($tournamentId);
            }
        } catch (\Throwable $e) {
            return view('admin.toornament.index', [
                'configured' => true,
                'tournaments' => [],
                'matches' => [],
                'tournamentId' => $tournamentId,
                'apiError' => $e->getMessage(),
            ]);
        }

        return view('admin.toornament.index', compact('tournaments', 'matches', 'tournamentId') + ['configured' => true]);
    }

    /**
     * Import a single game from Toornament as a new eBot match.
     * Returns JSON so the admin page can show success/error inline.
     */
    public function import(Request $request): JsonResponse
    {
        if (! $this->toornament->isConfigured()) {
            return response()->json(['status' => false, 'error' => 'not_configured']);
        }

        $tournamentId = $request->input('toornamentId');
        $matchId = $request->input('toornamentMatchId');
        $gameId = (int) $request->input('gameId', 1);

        if (! $tournamentId || ! $matchId) {
            return response()->json(['status' => false, 'error' => 'missing_params']);
        }

        try {
            $matchData = $this->toornament->getMatch($tournamentId, $matchId);
            $stage = $this->toornament->getStage($tournamentId, $matchData['stage_number'] ?? '1');

            $game = $matchData['games'][$gameId - 1] ?? null;

            if (! $game) {
                return response()->json(['status' => false, 'error' => 'game_not_found']);
            }

            $identifier = "{$tournamentId}.{$matchId}.{$gameId}";

            // Return existing match if already imported
            $existing = Matchs::where('identifier_id', $identifier)->first();
            if ($existing) {
                return response()->json(['status' => false, 'matchId' => $existing->id]);
            }

            $teamA = $matchData['opponents'][0]['participant']['name'] ?? 'Team A';
            $teamB = $matchData['opponents'][1]['participant']['name'] ?? 'Team B';
            $teamAFlag = $matchData['opponents'][0]['participant']['country'] ?? null;
            $teamBFlag = $matchData['opponents'][1]['participant']['country'] ?? null;
            $isGroup = ($stage['type'] ?? '') === 'group';

            $match = Matchs::create([
                'team_a_name' => $teamA,
                'team_b_name' => $teamB,
                'team_a_flag' => $teamAFlag,
                'team_b_flag' => $teamBFlag,
                'max_round' => config('ebot.default_max_round', 12),
                'overtime_startmoney' => config('ebot.default_overtime_startmoney', 10000),
                'overtime_max_round' => config('ebot.default_overtime_max_round', 3),
                'config_ot' => ! $isGroup,
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
     * Export match results back to Toornament.
     */
    public function export(Matchs $match): RedirectResponse
    {
        if (! $this->toornament->isConfigured()) {
            return back()->with('error', __('Toornament is not configured.'));
        }

        if (! $match->identifier_id) {
            return back()->with('error', __('This match is not linked to a Toornament game.'));
        }

        $ids = explode('.', $match->identifier_id);
        if (count($ids) < 3) {
            return back()->with('error', __('Invalid Toornament identifier.'));
        }

        [$tournamentId, $matchId, $gameId] = $ids;
        $gameId = (int) $gameId;

        try {
            $result = $this->toornament->getGameResult($tournamentId, $matchId, $gameId);

            $result['status'] = 'pending';
            if ($match->status > Matchs::STATUS_NOT_STARTED) {
                $result['status'] = 'running';
            }
            if ($match->status >= Matchs::STATUS_END_MATCH) {
                $result['status'] = 'completed';

                if ($match->score_a > $match->score_b) {
                    $result['opponents'][0]['result'] = 1; // win
                    $result['opponents'][1]['result'] = 3; // loss
                } elseif ($match->score_a < $match->score_b) {
                    $result['opponents'][0]['result'] = 3;
                    $result['opponents'][1]['result'] = 1;
                } else {
                    $result['opponents'][0]['result'] = 2; // draw
                    $result['opponents'][1]['result'] = 2;
                }
            }

            $match->load('maps');
            $firstMap = $match->maps->first();

            $result['map'] = $firstMap?->map_name ?? '';
            $result['opponents'][0]['score'] = $match->score_a;
            $result['opponents'][1]['score'] = $match->score_b;

            $this->toornament->putGameResult($tournamentId, $matchId, $gameId, $result);
            $this->toornament->patchGame($tournamentId, $matchId, $gameId, ['map' => $firstMap?->map_name ?? '']);

            return back()->with('success', __('Match results exported to Toornament.'));
        } catch (\Throwable $e) {
            return back()->with('error', __('Toornament export failed: :msg', ['msg' => $e->getMessage()]));
        }
    }
}
