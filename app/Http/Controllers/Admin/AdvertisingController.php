<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertising;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdvertisingController extends Controller
{
    public function index()
    {
        $ads = Advertising::with('season')->orderByDesc('id')->paginate(25);

        return view('admin.advertising.index', compact('ads'));
    }

    public function create()
    {
        $seasons = Season::orderByDesc('id')->get();

        return view('admin.advertising.create', compact('seasons'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['nullable', 'integer', 'exists:seasons,id'],
            'message' => ['required', 'string', 'max:1000'],
            'active' => ['boolean'],
        ]);

        Advertising::create($data);

        return redirect()->route('admin.advertising.index')
            ->with('success', __('Advertisement created.'));
    }

    public function edit(Advertising $advertising)
    {
        $seasons = Season::orderByDesc('id')->get();

        return view('admin.advertising.edit', compact('advertising', 'seasons'));
    }

    public function update(Request $request, Advertising $advertising): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['nullable', 'integer', 'exists:seasons,id'],
            'message' => ['required', 'string', 'max:1000'],
            'active' => ['boolean'],
        ]);

        $advertising->update($data);

        return redirect()->route('admin.advertising.index')
            ->with('success', __('Advertisement updated.'));
    }

    public function destroy(Advertising $advertising): RedirectResponse
    {
        $advertising->delete();

        return redirect()->route('admin.advertising.index')
            ->with('success', __('Advertisement deleted.'));
    }

    public function deactivate(Advertising $advertising): RedirectResponse
    {
        $advertising->update(['active' => ! $advertising->active]);

        $message = $advertising->active ? __('Advertisement activated.') : __('Advertisement deactivated.');

        return back()->with('success', $message);
    }
}
