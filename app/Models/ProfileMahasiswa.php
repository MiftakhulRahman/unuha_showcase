<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileMahasiswa extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nim', 'prodi_id', 'angkatan', 'semester', 'github_url', 'linkedin_url', 'portfolio_url'];
    protected $casts = ['angkatan' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
