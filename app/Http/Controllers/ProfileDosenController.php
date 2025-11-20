<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileDosenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_if(!auth()->user()->isDosen(), 403);
            return $next($request);
        });
    }

    public function edit()
    {
        $user = auth()->user()->load(['profileDosen']);
        
        return Inertia::render('Profiles/Dosen/Edit', [
            'user' => $user,
        ]);
    }
}