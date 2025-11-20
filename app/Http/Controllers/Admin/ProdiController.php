<?php

namespace App\Http\Controllers\Admin;

use App\Models\Prodi;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $query = Prodi::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('is_active', $status === 'active' ? true : false);
        }

        $prodis = $query->latest()->paginate(15)->appends($request->query());

        return Inertia::render('Admin/Prodis/Index', [
            'prodis' => $prodis,
            'filters' => [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Prodis/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:prodis',
            'kode' => 'required|string|unique:prodis',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Prodi::create($validated);

        return redirect()->route('admin.prodis.index')
            ->with('success', 'Program studi berhasil ditambahkan!');
    }

    public function show(Prodi $prodi)
    {
        return Inertia::render('Admin/Prodis/Show', [
            'prodi' => $prodi,
        ]);
    }

    public function edit(Prodi $prodi)
    {
        return Inertia::render('Admin/Prodis/Edit', [
            'prodi' => $prodi,
        ]);
    }

    public function update(Request $request, Prodi $prodi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:prodis,nama,' . $prodi->id,
            'kode' => 'required|string|unique:prodis,kode,' . $prodi->id,
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $prodi->update($validated);

        return redirect()->route('admin.prodis.show', $prodi)
            ->with('success', 'Program studi berhasil diperbarui!');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();

        return redirect()->route('admin.prodis.index')
            ->with('success', 'Program studi dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:prodis,id',
        ]);

        Prodi::whereIn('id', $validated['ids'])->delete();
        
        return redirect()->route('admin.prodis.index')
            ->with('success', count($validated['ids']) . ' program studi dihapus!');
    }
}
