<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Prodi;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'dosen')
            ->with(['profileDosen.prodi']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('profileDosen', function ($q) use ($search) {
                      $q->where('nidn', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('profileDosen', function ($q) use ($request) {
                $q->where('prodi_id', $request->get('prodi_id'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('is_active', $status === 'active' ? true : false);
        }

        $dosen = $query->latest()->paginate(15)->appends($request->query());
        $prodis = Prodi::where('is_active', true)->select('id', 'nama', 'kode')->get();

        return Inertia::render('Admin/Dosen/Index', [
            'dosen' => $dosen,
            'prodis' => $prodis,
            'filters' => [
                'search' => $request->get('search'),
                'prodi_id' => $request->get('prodi_id'),
                'status' => $request->get('status'),
            ],
        ]);
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'dosen', 404);
        return Inertia::render('Admin/Dosen/Show', [
            'dosen' => $user->load(['profileDosen.prodi']),
        ]);
    }

    public function edit(User $user)
    {
        abort_if($user->role !== 'dosen', 404);
        $prodis = Prodi::where('is_active', true)->get();
        
        return Inertia::render('Admin/Dosen/Edit', [
            'dosen' => $user->load(['profileDosen.prodi']),
            'prodis' => $prodis,
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'dosen', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'is_active' => 'boolean',
            'profile.nidn' => 'required|unique:profile_dosens,nidn,' . $user->profileDosen->id,
            'profile.prodi_id' => 'required|exists:prodis,id',
            'profile.jabatan' => 'nullable|string|max:100',
            'profile.bidang_keahlian' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        $user->profileDosen->update($validated['profile']);

        return redirect()->route('admin.dosen.show', $user)
            ->with('success', 'Data dosen berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'dosen', 404);
        $user->delete();
        
        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        User::where('role', 'dosen')
            ->whereIn('id', $validated['ids'])
            ->delete();
        
        return redirect()->route('admin.dosen.index')
            ->with('success', count($validated['ids']) . ' dosen dihapus!');
    }
}
