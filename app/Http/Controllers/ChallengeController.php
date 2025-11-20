<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use Inertia\Inertia;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::where('status', 'active')
            ->with('creator')
            ->latest('deadline')
            ->paginate(12);

        return Inertia::render('Challenges/Index', [
            'challenges' => $challenges,
        ]);
    }

    public function create()
    {
        // Hanya Dosen yang bisa buat challenge
        abort_if(!auth()->user()->isDosen(), 403);

        return Inertia::render('Challenges/Create');
    }

    public function store(StoreChallengeRequest $request)
    {
        $challenge = Challenge::create([
            'creator_id' => auth()->id(),
            'title' => $request->title,
            'slug' => str($request->title)->slug(),
            'description' => $request->description,
            'content' => $request->content,
            'level' => $request->level,
            'max_participants' => $request->max_participants,
            'start_date' => $request->start_date,
            'deadline' => $request->deadline,
            'criteria' => $request->criteria,
            'status' => 'draft',
        ]);

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Challenge created successfully!');
    }

    public function show(Challenge $challenge)
    {
        return Inertia::render('Challenges/Show', [
            'challenge' => $challenge->load('creator'),
        ]);
    }

    public function edit(Challenge $challenge)
    {
        $user = auth()->user();
        
        // Hanya creator (Dosen) atau Superadmin yang bisa edit
        if (!$user->isSuperAdmin() && $challenge->creator_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Challenges/Edit', [
            'challenge' => $challenge,
        ]);
    }

    public function update(UpdateChallengeRequest $request, Challenge $challenge)
    {
        $user = auth()->user();
        
        // Hanya creator (Dosen) atau Superadmin yang bisa update
        if (!$user->isSuperAdmin() && $challenge->creator_id !== $user->id) {
            abort(403);
        }

        $challenge->update($request->validated());

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Challenge updated successfully!');
    }

    public function destroy(Challenge $challenge)
    {
        $user = auth()->user();
        
        // Hanya creator (Dosen) atau Superadmin yang bisa delete
        if (!$user->isSuperAdmin() && $challenge->creator_id !== $user->id) {
            abort(403);
        }

        $challenge->delete();

        return redirect()->route('challenges.index')
            ->with('success', 'Challenge deleted successfully!');
    }

    public function publish(Challenge $challenge)
    {
        $user = auth()->user();
        
        // Hanya creator (Dosen) atau Superadmin yang bisa publish
        if (!$user->isSuperAdmin() && $challenge->creator_id !== $user->id) {
            abort(403);
        }

        $challenge->update(['status' => 'active']);

        return back()->with('success', 'Challenge published!');
    }
}
