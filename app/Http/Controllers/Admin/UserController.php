<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('username')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username'        => ['required', 'string', 'max:128', 'unique:sf_guard_user,username'],
            'email_address'   => ['required', 'email', 'max:255', 'unique:sf_guard_user,email_address'],
            'first_name'      => ['nullable', 'string', 'max:255'],
            'last_name'       => ['nullable', 'string', 'max:255'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'is_super_admin'  => ['boolean'],
            'is_active'       => ['boolean'],
        ]);

        User::create([
            'username'       => $data['username'],
            'email_address'  => $data['email_address'],
            'first_name'     => $data['first_name'] ?? null,
            'last_name'      => $data['last_name'] ?? null,
            'password'       => Hash::make($data['password']),
            'algorithm'      => 'bcrypt',
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'is_active'      => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('User created.'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'username'        => ['required', 'string', 'max:128', 'unique:sf_guard_user,username,' . $user->id],
            'email_address'   => ['required', 'email', 'max:255', 'unique:sf_guard_user,email_address,' . $user->id],
            'first_name'      => ['nullable', 'string', 'max:255'],
            'last_name'       => ['nullable', 'string', 'max:255'],
            'password'        => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_super_admin'  => ['boolean'],
            'is_active'       => ['boolean'],
        ]);

        $updateData = [
            'username'       => $data['username'],
            'email_address'  => $data['email_address'],
            'first_name'     => $data['first_name'] ?? null,
            'last_name'      => $data['last_name'] ?? null,
            'is_super_admin' => $data['is_super_admin'] ?? false,
            'is_active'      => $data['is_active'] ?? true,
        ];

        if (! empty($data['password'])) {
            $updateData['password']  = Hash::make($data['password']);
            $updateData['algorithm'] = 'bcrypt';
            $updateData['salt']      = null;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', __('User updated.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('User deleted.'));
    }
}
