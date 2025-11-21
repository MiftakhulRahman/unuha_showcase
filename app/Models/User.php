<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'avatar',
        'bio',
        'role',
        'is_active',
        'registration_completed',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'registration_completed' => 'boolean',
        ];
    }

    public function profileMahasiswa(): HasOne
    {
        return $this->hasOne(ProfileMahasiswa::class);
    }

    public function profileDosen(): HasOne
    {
        return $this->hasOne(ProfileDosen::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class, 'creator_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}
