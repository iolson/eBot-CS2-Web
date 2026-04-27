<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::orderByDesc('id')->paginate(25);

        return view('admin.seasons.index', compact('seasons'));
    }

    public function create()
    {
        return view('admin.seasons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'event'  => ['nullable', 'string', 'max:255'],
            'link'   => ['nullable', 'url', 'max:255'],
            'logo'   => ['nullable', 'url', 'max:255'],
            'start'  => ['nullable', 'date'],
            'end'    => ['nullable', 'date', 'after_or_equal:start'],
            'active' => ['boolean'],
        ]);

        Season::create($data);

        return redirect()->route('admin.seasons.index')
            ->with('success', __('Season created.'));
    }

    public function edit(Season $season)
    {
        return view('admin.seasons.edit', compact('season'));
    }

    public function update(Request $request, Season $season): RedirectResponse
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'event'  => ['nullable', 'string', 'max:255'],
            'link'   => ['nullable', 'url', 'max:255'],
            'logo'   => ['nullable', 'url', 'max:255'],
            'start'  => ['nullable', 'date'],
            'end'    => ['nullable', 'date', 'after_or_equal:start'],
            'active' => ['boolean'],
        ]);

        $season->update($data);

        return redirect()->route('admin.seasons.index')
            ->with('success', __('Season updated.'));
    }

    public function destroy(Season $season): RedirectResponse
    {
        $season->delete();

        return redirect()->route('admin.seasons.index')
            ->with('success', __('Season deleted.'));
    }

    public function deactivate(Season $season): RedirectResponse
    {
        $season->update(['active' => ! $season->active]);

        $message = $season->active ? __('Season activated.') : __('Season deactivated.');

        return back()->with('success', $message);
    }
}
