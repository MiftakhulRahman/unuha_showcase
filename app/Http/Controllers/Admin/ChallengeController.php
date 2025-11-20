<?php

namespace App\Http\Controllers\Admin;

use App\Models\Challenge;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $query = Challenge::with(['user'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $challenges = $query->paginate(15)->appends($request->query());

        return Inertia::render('Admin/Challenges/Index', [
            'challenges' => $challenges,
            'filters' => [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
            ],
        ]);
    }

    public function show(Challenge $challenge)
    {
        return Inertia::render('Admin/Challenges/Show', [
            'challenge' => $challenge->load(['user']),
        ]);
    }

    public function edit(Challenge $challenge)
    {
        return Inertia::render('Admin/Challenges/Edit', [
            'challenge' => $challenge->load(['user']),
        ]);
    }

    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:draft,active,finished',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $challenge->update($validated);

        return redirect()->route('admin.challenges.index')
            ->with('success', 'Challenge berhasil diperbarui!');
    }

    public function destroy(Challenge $challenge)
    {
        $challenge->delete();

        return redirect()->route('admin.challenges.index')
            ->with('success', 'Challenge berhasil dibatalkan!');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:challenges,id',
        ]);

        Challenge::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('admin.challenges.index')
            ->with('success', count($validated['ids']) . ' challenge berhasil dihapus!');
    }
}
