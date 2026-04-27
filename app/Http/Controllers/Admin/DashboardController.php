<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchs;
use App\Models\Server;

class DashboardController extends Controller
{
    public function index()
    {
        $liveMatches  = Matchs::live()->with(['teamA', 'teamB', 'server'])->get();
        $pendingCount = Matchs::notStarted()->count();
        $serverCount  = Server::count();

        return view('admin.dashboard', compact('liveMatches', 'pendingCount', 'serverCount'));
    }
}
