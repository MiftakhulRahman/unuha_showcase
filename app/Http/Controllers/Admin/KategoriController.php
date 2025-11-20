<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KategoriController extends Controller
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
        $kategoris = Kategori::latest()->paginate(15);
        return Inertia::render('Admin/Kategoris/Index', ['kategoris' => $kategoris]);
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
}
