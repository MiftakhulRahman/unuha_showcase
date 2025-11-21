<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profileMahasiswa', 'profileDosen']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('is_active', $status === 'active' ? true : false);
        }

        // Filter by registration status
        if ($request->filled('registration')) {
            $registration = $request->get('registration');
            $query->where('registration_completed', $registration === 'completed' ? true : false);
        }

        $users = $query->latest()->paginate(15)->appends($request->query());

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $request->get('search'),
                'role' => $request->get('role'),
                'status' => $request->get('status'),
                'registration' => $request->get('registration'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,dosen,mahasiswa',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        $tempPassword = \Str::random(12);
        $user->update(['password' => bcrypt($tempPassword)]);
        return back()->with('success', "Password direset. Password sementara: $tempPassword");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        User::whereIn('id', $validated['ids'])->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', count($validated['ids']) . ' pengguna berhasil dihapus.');
    }
}
