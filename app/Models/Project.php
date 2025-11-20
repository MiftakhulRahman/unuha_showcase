<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'kategori_id', 'title', 'slug', 'description', 'content', 'thumbnail', 'banner_image', 'repository_url', 'demo_url', 'video_url', 'status', 'is_featured', 'published_at'];
    protected $casts = ['published_at' => 'datetime', 'is_featured' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'project_tool');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function interactions()
    {
        return $this->morphMany(Interaction::class, 'interactable');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
