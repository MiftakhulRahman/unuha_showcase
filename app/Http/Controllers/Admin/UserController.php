<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_if(!auth()->user()->isSuperAdmin(), 403);
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with(['profileMahasiswa', 'profileDosen'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user->load(['profileMahasiswa', 'profileDosen']),
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load(['profileMahasiswa', 'profileDosen']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role' => 'required|in:superadmin,dosen,mahasiswa',
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    public function resetPassword(User $user)
    {
        $tempPassword = \Str::random(12);
        $user->update(['password' => bcrypt($tempPassword)]);
        return back()->with('success', "Password reset. Temp password: $tempPassword");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted!');
    }
}
