<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Matchs;

class WidgetController extends Controller
{
    public function matchPlayers(Matchs $match)
    {
        $match->load(['teamA', 'teamB', 'maps.players']);

        return view('widget.match-players', compact('match'));
    }

    public function liveStats()
    {
        $matches = Matchs::live()
            ->with(['teamA', 'teamB', 'maps.players'])
            ->limit(15)
            ->get();

        return view('widget.live-stats', compact('matches'));
    }
}
