<?php

namespace Modules\SocialSync\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Modules\SocialSync\app\Enums\SocialProvider;
use Modules\SocialSync\Casts\EncryptedJsonCast;
use Modules\User\Models\User;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'meta',
        'credentials',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
        'credentials',
        'token_expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'meta' => 'array',
            'credentials' => EncryptedJsonCast::class,
            'provider' => SocialProvider::class,
        ];
    }

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute($value): ?string
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            Log::error("Failed to decrypt access token for account: " . $this->id);
            return null;
        }
    }

    /**
     * Get the user that owns the social account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the posts for the social account.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_social_accounts')
            ->withPivot(['external_post_id', 'status', 'error_message'])
            ->withTimestamps();
    }
}

