<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
{
    public function index()
    {
        $query = Kategori::query();

        // Search
        if (request()->filled('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if (request()->filled('status')) {
            $status = request()->get('status');
            $query->where('is_active', $status === 'active' ? true : false);
        }

        $kategoris = $query->latest()->paginate(15)->appends(request()->query());
        return Inertia::render('Admin/Kategoris/Index', ['kategoris' => $kategoris, 'filters' => [
            'search' => request()->get('search'),
            'status' => request()->get('status'),
        ]]);
    }

    public function create()
    {
        return Inertia::render('Admin/Kategoris/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:kategoris',
            'slug' => 'required|string|unique:kategoris',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Kategori::create($validated);
        return redirect()->route('admin.kategoris.index')
            ->with('success', 'Kategori created!');
    }

    public function edit(Kategori $kategori)
    {
        return Inertia::render('Admin/Kategoris/Edit', ['kategori' => $kategori]);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:kategoris,nama,' . $kategori->id,
            'slug' => 'required|string|unique:kategoris,slug,' . $kategori->id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $kategori->update($validated);
        return redirect()->route('admin.kategoris.index')
            ->with('success', 'Kategori updated!');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('admin.kategoris.index')
            ->with('success', 'Kategori deleted!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:kategoris,id',
        ]);

        Kategori::whereIn('id', $validated['ids'])->delete();
        
        return redirect()->route('admin.kategoris.index')
            ->with('success', count($validated['ids']) . ' kategoris deleted!');
    }
}
