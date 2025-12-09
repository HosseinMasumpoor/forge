<?php

namespace Modules\SocialSync\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\SocialSync\app\Enums\PostSocialAccountStatus;
use Modules\SocialSync\app\Models\Post;
use Modules\SocialSync\app\Models\PostSocialAccount;
use Modules\SocialSync\app\Models\SocialAccount;
use Modules\SocialSync\app\Services\SocialMediaProviderResolver;
use Throwable;

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $postId,
        public int $socialAccountId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = Post::find($this->postId);
        $socialAccount = SocialAccount::find($this->socialAccountId);

        if (!$post || !$socialAccount) {
            $this->fail(new \Exception('Dependency not found'));
            return;
        }

        $pivotRecord = PostSocialAccount::where('post_id', $this->postId)
            ->where('social_account_id', $this->socialAccountId)
            ->first();

        if (!$pivotRecord) {
            $this->fail(new \Exception('Pivot record not found'));
            return;
        }

        $pivotRecord->update(['status' => PostSocialAccountStatus::PROCESSING]);

        try {
            $providerService = SocialMediaProviderResolver::resolve($socialAccount->provider);

            $externalId = $providerService->publish($post->content, $post->media_url, ['access_token' => $socialAccount->access_token]);

            $pivotRecord->update([
                'external_post_id' => $externalId,
                'status' => PostSocialAccountStatus::PUBLISHED,
                'error_message' => null,
                'published_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("Publish failed for provider {$socialAccount->provider}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $postSocialAccount = PostSocialAccount::where('post_id', $this->postId)
            ->where('social_account_id', $this->socialAccountId)
            ->first();

        if ($postSocialAccount) {
            $postSocialAccount->update([
                'status' => PostSocialAccountStatus::FAILED,
                'error_message' => $exception->getMessage(),
            ]);
        }

        Log::error('PublishPostJob: Job failed after all retries', [
            'post_id' => $this->postId,
            'social_account_id' => $this->socialAccountId,
            'error' => $exception->getMessage(),
        ]);
    }
}

