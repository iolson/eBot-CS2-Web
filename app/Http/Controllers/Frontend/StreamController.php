<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Matchs;

class StreamController extends Controller
{
    public function show(Matchs $match)
    {
        $match->load(['teamA', 'teamB', 'server', 'maps']);

        return view('stream.show', compact('match'));
    }
}
