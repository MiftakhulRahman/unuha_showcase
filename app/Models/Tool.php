<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'slug', 'deskripsi', 'icon', 'color', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_tool');
    }
}
