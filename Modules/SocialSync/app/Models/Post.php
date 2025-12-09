<?php

namespace Modules\SocialSync\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\SocialSync\app\Enums\PostStatus;
use Modules\User\Models\User;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
        'content',
        'media_path',
        'status',
        'user_id',
        'tags',
        'scheduled_at',
        'published_at',
        'n8n_execution_id',
    ];

    protected $appends = [
        'media_url'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'status' => PostStatus::class,
        ];
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the social accounts for the post.
     */
    public function socialAccounts(): BelongsToMany
    {
        return $this->belongsToMany(SocialAccount::class, 'post_social_accounts')
            ->withPivot(['external_post_id', 'status', 'error_message'])
            ->withTimestamps();
    }

    public function getMediaUrlAttribute(): ?string
    {
        if(empty($this->media_path)){
            return null;
        }
        return route('api.posts.media', $this->id);
    }
}

