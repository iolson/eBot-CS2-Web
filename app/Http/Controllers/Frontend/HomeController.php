<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Matchs;

class HomeController extends Controller
{
    public function index()
    {
        $matches = Matchs::active()
            ->with(['teamA', 'teamB', 'server', 'season'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('home', compact('matches'));
    }
}
