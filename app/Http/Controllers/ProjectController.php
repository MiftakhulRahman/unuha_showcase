<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Kategori;
use App\Models\Tool;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Super Admin melihat semua projects
        if ($user->isSuperAdmin()) {
            $projects = Project::with('user', 'kategori', 'tools')
                ->latest()
                ->paginate(12);
        } else {
            // User lain hanya melihat project mereka sendiri
            $projects = Project::where('user_id', $user->id)
                ->with('kategori', 'tools')
                ->latest()
                ->paginate(12);
        }

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $kategoris = Kategori::where('is_active', true)->get();
        $tools = Tool::where('is_active', true)->get();

        return Inertia::render('Projects/Create', [
            'kategoris' => $kategoris,
            'tools' => $tools,
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            'user_id' => auth()->id(),
            'kategori_id' => $request->kategori_id,
            'title' => $request->title,
            'slug' => str($request->title)->slug(),
            'description' => $request->description,
            'content' => $request->content,
            'repository_url' => $request->repository_url,
            'demo_url' => $request->demo_url,
            'video_url' => $request->video_url,
            'status' => 'draft',
        ]);

        if ($request->has('tool_ids')) {
            $project->tools()->attach($request->tool_ids);
        }

        return redirect()
            ->route('projects.edit', $project)
            ->with('success', 'Project created successfully!');
    }

    public function show(Project $project)
    {
        $user = auth()->user();
        
        // Superadmin bisa lihat semua, user lain hanya project mereka
        if (!$user->isSuperAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Projects/Show', [
            'project' => $project->load('kategori', 'tools', 'images'),
        ]);
    }

    public function edit(Project $project)
    {
        $user = auth()->user();
        
        // Superadmin bisa edit semua, user lain hanya project mereka
        if (!$user->isSuperAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }

        $kategoris = Kategori::where('is_active', true)->get();
        $tools = Tool::where('is_active', true)->get();

        return Inertia::render('Projects/Edit', [
            'project' => $project->load('tools', 'images'),
            'kategoris' => $kategoris,
            'tools' => $tools,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $user = auth()->user();
        
        // Superadmin bisa update semua, user lain hanya project mereka
        if (!$user->isSuperAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }

        $project->update($request->validated());

        if ($request->has('tool_ids')) {
            $project->tools()->sync($request->tool_ids);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        $user = auth()->user();
        
        // Superadmin bisa delete semua, user lain hanya project mereka
        if (!$user->isSuperAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    public function publish(Project $project)
    {
        $user = auth()->user();
        
        // Superadmin bisa publish semua, user lain hanya project mereka
        if (!$user->isSuperAdmin() && $project->user_id !== $user->id) {
            abort(403);
        }

        $project->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Project published!');
    }
}
