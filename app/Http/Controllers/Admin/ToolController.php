<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tool;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
{
    public function index()
    {
        $query = Tool::query();

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

        $tools = $query->latest()->paginate(15)->appends(request()->query());
        return Inertia::render('Admin/Tools/Index', ['tools' => $tools, 'filters' => [
            'search' => request()->get('search'),
            'status' => request()->get('status'),
        ]]);
    }

    public function create()
    {
        return Inertia::render('Admin/Tools/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:tools',
            'slug' => 'required|string|unique:tools',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Tool::create($validated);
        return redirect()->route('admin.tools.index')
            ->with('success', 'Tool created!');
    }

    public function edit(Tool $tool)
    {
        return Inertia::render('Admin/Tools/Edit', ['tool' => $tool]);
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'nama' => 'required|string|unique:tools,nama,' . $tool->id,
            'slug' => 'required|string|unique:tools,slug,' . $tool->id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $tool->update($validated);
        return redirect()->route('admin.tools.index')
            ->with('success', 'Tool updated!');
    }

    public function destroy(Tool $tool)
    {
        $tool->delete();
        return redirect()->route('admin.tools.index')
            ->with('success', 'Tool deleted!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:tools,id',
        ]);

        Tool::whereIn('id', $validated['ids'])->delete();
        
        return redirect()->route('admin.tools.index')
            ->with('success', count($validated['ids']) . ' tools deleted!');
    }
}