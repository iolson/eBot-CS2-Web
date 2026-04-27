<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $activeSeasons = Season::active()->orderByDesc('start')->get();
        $pastSeasons = Season::where('active', false)->orderByDesc('start')->limit(5)->get();

        return view('seasons.index', compact('activeSeasons', 'pastSeasons'));
    }

    public function select(Request $request, Season $season): RedirectResponse
    {
        session()->put('selected_season_id', $season->id);

        $redirect = $request->input('site', 'matchs');

        return match ($redirect) {
            'archived' => redirect()->route('matchs.archived'),
            default => redirect()->route('matchs.index'),
        };
    }
}
