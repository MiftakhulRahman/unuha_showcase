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
    // Route model binding for nested resources
    Route::bind('dosen', function ($value) {
        return \App\Models\User::findOrFail($value);
    });
    Route::bind('mahasiswa', function ($value) {
        return \App\Models\User::findOrFail($value);
    });
    Route::bind('prodi', function ($value) {
        return \App\Models\Prodi::findOrFail($value);
    });
    Route::bind('kategori', function ($value) {
        return \App\Models\Kategori::findOrFail($value);
    });
    Route::bind('tool', function ($value) {
        return \App\Models\Tool::findOrFail($value);
    });
    Route::bind('project', function ($value) {
        return \App\Models\Project::findOrFail($value);
    });
    Route::bind('challenge', function ($value) {
        return \App\Models\Challenge::findOrFail($value);
    });

    Route::middleware('admin.superadmin')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::post('users/bulk-delete', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('users.bulkDelete');

        Route::resource('kategoris', \App\Http\Controllers\Admin\KategoriController::class);
        Route::post('kategoris/bulk-delete', [\App\Http\Controllers\Admin\KategoriController::class, 'bulkDelete'])->name('kategoris.bulkDelete');
        
        Route::resource('tools', \App\Http\Controllers\Admin\ToolController::class);
        Route::post('tools/bulk-delete', [\App\Http\Controllers\Admin\ToolController::class, 'bulkDelete'])->name('tools.bulkDelete');

        Route::resource('dosen', \App\Http\Controllers\Admin\DosenController::class);
        Route::post('dosen/bulk-delete', [\App\Http\Controllers\Admin\DosenController::class, 'bulkDelete'])->name('dosen.bulkDelete');
        
        Route::resource('mahasiswa', \App\Http\Controllers\Admin\MahasiswaController::class);
        Route::post('mahasiswa/bulk-delete', [\App\Http\Controllers\Admin\MahasiswaController::class, 'bulkDelete'])->name('mahasiswa.bulkDelete');
        
        Route::resource('prodis', \App\Http\Controllers\Admin\ProdiController::class);
        Route::post('prodis/bulk-delete', [\App\Http\Controllers\Admin\ProdiController::class, 'bulkDelete'])->name('prodis.bulkDelete');
        
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class, ['except' => ['create', 'store']]);
        Route::post('projects/bulk-delete', [\App\Http\Controllers\Admin\ProjectController::class, 'bulkDelete'])->name('projects.bulkDelete');
        
        Route::resource('challenges', \App\Http\Controllers\Admin\ChallengeController::class, ['except' => ['create', 'store']]);
        Route::post('challenges/bulk-delete', [\App\Http\Controllers\Admin\ChallengeController::class, 'bulkDelete'])->name('challenges.bulkDelete');
    });
});

// Additional routes for dosen and mahasiswa specific features
Route::middleware(['auth', 'verified'])->group(function () {
    // Challenge evaluation by dosen
    Route::get('penilaian', [\App\Http\Controllers\EvaluationController::class, 'index'])->name('evaluations.index');

    // Profile management based on user role
    Route::get('profile/dosen', [\App\Http\Controllers\ProfileDosenController::class, 'edit'])->name('profile.dosen.edit');
    Route::get('profile/mahasiswa', [\App\Http\Controllers\ProfileMahasiswaController::class, 'edit'])->name('profile.mahasiswa.edit');

    // Collaboration management
    Route::get('kolaborasi', [\App\Http\Controllers\CollaborationController::class, 'index'])->name('collaborations.index');
});

require __DIR__.'/settings.php';
