<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use League\CommonMark\CommonMarkConverter;

class Note extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'user_id',
        'title', 
        'content',
        'visibility',
        'slug'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Sluggable configuration
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharedWithUsers()
    {
        return $this->belongsToMany(User::class, 'note_shares', 'note_id', 'shared_with_user_id')
                    ->withPivot('permission')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'asc');
    }

    public function shares()
    {
        return $this->hasMany(NoteShare::class);
    }

    // Accessors & Mutators
    public function getMarkdownContentAttribute()
    {
        $converter = new CommonMarkConverter();
        return $converter->convert($this->content);
    }

    public function getExcerptAttribute()
    {
        return \Str::limit(strip_tags($this->content), 150);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function scopeShared($query)
    {
        return $query->where('visibility', 'shared');
    }

    // Helper methods
    public function canBeViewedBy(User $user = null)
    {
        if (!$user) {
            return $this->visibility === 'public';
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->visibility === 'public') {
            return true;
        }

        if ($this->visibility === 'shared') {
            return $this->sharedWithUsers()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    public function canBeEditedBy(User $user)
    {
        return $this->user_id === $user->id;
    }
}