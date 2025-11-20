<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileDosen extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nidn', 'prodi_id', 'jabatan', 'bidang_keahlian', 'scholar_url', 'scopus_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
