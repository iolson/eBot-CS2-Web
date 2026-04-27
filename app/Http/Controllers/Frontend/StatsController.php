<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Matchs;
use App\Models\Player;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $totalKills    = Player::sum('nb_kill');
        $totalDeaths   = Player::sum('death');
        $totalHS       = Player::sum('hs');
        $liveCount     = Matchs::live()->count();
        $finishedCount = Matchs::finished()->count();
        $pendingCount  = Matchs::notStarted()->count();
        $serverCount   = Server::count();

        return view('stats.index', compact(
            'totalKills',
            'totalDeaths',
            'totalHS',
            'liveCount',
            'finishedCount',
            'pendingCount',
            'serverCount'
        ));
    }

    public function global(Request $request)
    {
        $matchIds = $request->input('match_ids', []);

        $playerQuery = Player::select(
            'steamid',
            DB::raw('MAX(pseudo) as name'),
            DB::raw('SUM(nb_kill) as total_kills'),
            DB::raw('SUM(death) as total_deaths'),
            DB::raw('SUM(assist) as total_assists'),
            DB::raw('SUM(hs) as total_hs')
        )
        ->groupBy('steamid')
        ->having('total_kills', '>', 0);

        if (! empty($matchIds)) {
            $playerQuery->whereHas('map', fn ($q) => $q->whereIn('match_id', $matchIds));
        }

        $players = $playerQuery->orderByDesc('total_kills')->paginate(50);

        return view('stats.global', compact('players'));
    }

    public function player(string $steamid)
    {
        $stats = Player::where('steamid', $steamid)
            ->select(
                DB::raw('MAX(pseudo) as name'),
                DB::raw('SUM(nb_kill) as total_kills'),
                DB::raw('SUM(death) as total_deaths'),
                DB::raw('SUM(assist) as total_assists'),
                DB::raw('SUM(hs) as total_hs'),
                DB::raw('COUNT(*) as matches_played')
            )
            ->first();

        if (! $stats || ! $stats->total_kills) {
            abort(404, 'Player not found.');
        }

        $recentMaps = Player::where('steamid', $steamid)
            ->with(['map.match.teamA', 'map.match.teamB'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('stats.player', compact('steamid', 'stats', 'recentMaps'));
    }

    public function maps()
    {
        $mapStats = DB::table('maps')
            ->join('matchs', 'maps.match_id', '=', 'matchs.id')
            ->where('matchs.status', '>=', Matchs::STATUS_END_MATCH)
            ->select(
                'maps.map_name',
                DB::raw('COUNT(*) as times_played'),
                DB::raw('SUM(CASE WHEN maps.score_1 > maps.score_2 THEN 1 ELSE 0 END) as team1_wins'),
                DB::raw('SUM(CASE WHEN maps.score_2 > maps.score_1 THEN 1 ELSE 0 END) as team2_wins')
            )
            ->groupBy('maps.map_name')
            ->orderByDesc('times_played')
            ->get();

        return view('stats.maps', compact('mapStats'));
    }

    public function weapons()
    {
        $weaponStats = DB::table('player_kill')
            ->select(
                'weapon',
                DB::raw('COUNT(*) as total_kills'),
                DB::raw('SUM(CASE WHEN headshot = 1 THEN 1 ELSE 0 END) as headshots')
            )
            ->whereNotNull('weapon')
            ->groupBy('weapon')
            ->orderByDesc('total_kills')
            ->get();

        return view('stats.weapons', compact('weaponStats'));
    }

    public function entryKills()
    {
        // Entry frag analysis: first kill each round per player
        $entryStats = DB::table('player_kill')
            ->select(
                'killer_id',
                DB::raw('MAX(killer_name) as name'),
                DB::raw('COUNT(*) as entry_kills')
            )
            ->whereNotNull('killer_id')
            ->groupBy('killer_id')
            ->orderByDesc('entry_kills')
            ->limit(50)
            ->get();

        return view('stats.entry-kills', compact('entryStats'));
    }

    public function gunRound()
    {
        // Gun round statistics (pistol/eco round analysis)
        $gunRoundStats = DB::table('round_summary')
            ->select(
                'team_win',
                DB::raw('COUNT(*) as rounds_played'),
                DB::raw('SUM(CASE WHEN ct_win = 1 THEN 1 ELSE 0 END) as ct_wins'),
                DB::raw('SUM(CASE WHEN t_win = 1 THEN 1 ELSE 0 END) as t_wins')
            )
            ->groupBy('team_win')
            ->get();

        return view('stats.gunround', compact('gunRoundStats'));
    }
}
