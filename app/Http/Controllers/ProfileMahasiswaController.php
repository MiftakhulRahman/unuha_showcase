<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileMahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_if(!auth()->user()->isMahasiswa(), 403);
            return $next($request);
        });
    }

    public function edit()
    {
        $user = auth()->user()->load(['profileMahasiswa']);
        
        return Inertia::render('Profiles/Mahasiswa/Edit', [
            'user' => $user,
        ]);
    }
}