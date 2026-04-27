<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::orderBy('name')->paginate(25);

        return view('admin.configs.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.configs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:configs,name'],
            'content' => ['nullable', 'string'],
        ]);

        Config::create($data);

        return redirect()->route('admin.configs.index')
            ->with('success', __('Config created.'));
    }

    public function edit(Config $config)
    {
        return view('admin.configs.edit', compact('config'));
    }

    public function update(Request $request, Config $config): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255', 'unique:configs,name,' . $config->id],
            'content' => ['nullable', 'string'],
        ]);

        $config->update($data);

        return redirect()->route('admin.configs.index')
            ->with('success', __('Config updated.'));
    }

    public function destroy(Config $config): RedirectResponse
    {
        $config->delete();

        return redirect()->route('admin.configs.index')
            ->with('success', __('Config deleted.'));
    }
}
