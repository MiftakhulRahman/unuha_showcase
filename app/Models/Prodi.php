<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode', 'deskripsi', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function profileMahasiswas(): HasMany
    {
        return $this->hasMany(ProfileMahasiswa::class);
    }

    public function profileDosens(): HasMany
    {
        return $this->hasMany(ProfileDosen::class);
    }
}
