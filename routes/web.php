<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ChallengeController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Project Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/publish', [ProjectController::class, 'publish'])->name('projects.publish');
});

// Challenge Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('challenges', ChallengeController::class);
    Route::post('challenges/{challenge}/publish', [ChallengeController::class, 'publish'])->name('challenges.publish');
});

// Admin Routes (Superadmin Only)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.resetPassword');
    
    Route::resource('kategoris', \App\Http\Controllers\Admin\KategoriController::class);
    Route::resource('tools', \App\Http\Controllers\Admin\ToolController::class);
});

require __DIR__.'/settings.php';
