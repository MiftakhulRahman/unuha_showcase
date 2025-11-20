<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tool;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ToolController extends Controller
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
        $tools = Tool::latest()->paginate(15);
        return Inertia::render('Admin/Tools/Index', ['tools' => $tools]);
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
}
