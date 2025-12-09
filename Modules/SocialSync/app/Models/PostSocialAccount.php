<?php

namespace Modules\SocialSync\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\SocialSync\app\Enums\PostSocialAccountStatus;

class PostSocialAccount extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_social_accounts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'post_id',
        'social_account_id',
        'external_post_id',
        'status',
        'error_message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostSocialAccountStatus::class,
        ];
    }

    /**
     * Get the post that owns the post social account.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the social account that owns the post social account.
     */
    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

}

