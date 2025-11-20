<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['creator_id', 'title', 'slug', 'description', 'content', 'thumbnail', 'level', 'status', 'max_participants', 'start_date', 'deadline', 'criteria'];
    protected $casts = ['start_date' => 'datetime', 'deadline' => 'datetime'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
