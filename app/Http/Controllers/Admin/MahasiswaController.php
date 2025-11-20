<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Prodi;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'mahasiswa')
            ->with(['profileMahasiswa.prodi']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhereHas('profileMahasiswa', function ($q) use ($search) {
                      $q->where('nim', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by prodi
        if ($request->filled('prodi_id')) {
            $query->whereHas('profileMahasiswa', function ($q) use ($request) {
                $q->where('prodi_id', $request->get('prodi_id'));
            });
        }

        // Filter by angkatan
        if ($request->filled('angkatan')) {
            $query->whereHas('profileMahasiswa', function ($q) use ($request) {
                $q->where('angkatan', $request->get('angkatan'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('is_active', $status === 'active' ? true : false);
        }

        $mahasiswa = $query->latest()->paginate(15)->appends($request->query());
        $prodis = Prodi::where('is_active', true)->select('id', 'nama', 'kode')->get();

        return Inertia::render('Admin/Mahasiswa/Index', [
            'mahasiswa' => $mahasiswa,
            'prodis' => $prodis,
            'filters' => [
                'search' => $request->get('search'),
                'prodi_id' => $request->get('prodi_id'),
                'angkatan' => $request->get('angkatan'),
                'status' => $request->get('status'),
            ],
        ]);
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'mahasiswa', 404);
        return Inertia::render('Admin/Mahasiswa/Show', [
            'mahasiswa' => $user->load(['profileMahasiswa.prodi']),
        ]);
    }

    public function edit(User $user)
    {
        abort_if($user->role !== 'mahasiswa', 404);
        $prodis = Prodi::where('is_active', true)->get();
        
        return Inertia::render('Admin/Mahasiswa/Edit', [
            'mahasiswa' => $user->load(['profileMahasiswa.prodi']),
            'prodis' => $prodis,
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'mahasiswa', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|unique:users,username,' . $user->id,
            'is_active' => 'boolean',
            'profile.nim' => 'required|unique:profile_mahasiswas,nim,' . $user->profileMahasiswa->id,
            'profile.prodi_id' => 'required|exists:prodis,id',
            'profile.angkatan' => 'required|integer|min:2000|max:2999',
            'profile.semester' => 'required|integer|min:1|max:8',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'is_active' => $validated['is_active'] ?? false,
        ]);

        $user->profileMahasiswa->update($validated['profile']);

        return redirect()->route('admin.mahasiswa.show', $user)
            ->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'mahasiswa', 404);
        $user->delete();
        
        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:users,id',
        ]);

        User::where('role', 'mahasiswa')
            ->whereIn('id', $validated['ids'])
            ->delete();
        
        return redirect()->route('admin.mahasiswa.index')
            ->with('success', count($validated['ids']) . ' mahasiswa dihapus!');
    }
}
