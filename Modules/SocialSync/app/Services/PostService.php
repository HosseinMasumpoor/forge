<?php

namespace Modules\SocialSync\app\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\StorageService;
use Modules\Core\Traits\Media;
use Modules\SocialSync\app\Enums\PostStatus;
use Modules\SocialSync\app\Events\PostCreated;
use Modules\SocialSync\app\Interfaces\Repositories\PostRepositoryInterface;
use Modules\SocialSync\app\Interfaces\Repositories\SocialAccountRepositoryInterface;
use Modules\SocialSync\app\Jobs\PublishPostJob;
use Modules\SocialSync\app\Models\Post;

class PostService
{
    use Media;

    protected string $mediaFolder = 'posts/media';

    public function __construct(
        protected PostRepositoryInterface $repository,
        protected SocialAccountRepositoryInterface $socialAccountRepository,
        protected N8NService $n8nService
    ) {}

    public function list()
    {
        return $this->repository->index();
    }

    public function getById(string $id)
    {
        return $this->repository->findByField('id', $id);
    }

    public function getByUserId(string $userId)
    {
        return $this->repository->getByUserId($userId);
    }

    public function create(array $data)
    {
        return $this->repository->newItem($data);
    }

    public function createWithAccounts(string $userId, array $data, array $socialAccountIds)
    {
        $data['media_path'] = $this->storeMedia($userId, $data["media_path"]);
        $isScheduled = !empty($data['scheduled_at']);

        try {
            DB::beginTransaction();
            $postData = array_merge($data, [
                'user_id' => $userId,
                'status' => $isScheduled ? PostStatus::SCHEDULED : PostStatus::DRAFT,
            ]);

            $post = $this->repository->newItem($postData);

            $this->repository->attachSocialAccounts($post->id, $socialAccountIds);


            if (!$isScheduled) {
                $post->update(['status' => PostStatus::PENDING]);

                foreach ($socialAccountIds as $socialAccountId) {
                    PublishPostJob::dispatch($post->id, $socialAccountId);
                }

                $this->setPublishStatus($post->id);
            }


            DB::commit();

            $post = $post->fresh();

            event(new PostCreated($post));

            return $post->load('socialAccounts');
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }

    public function update(string $id, array $data)
    {
        return $this->repository->updateItem($id, $data);
    }

    public function delete(string $id)
    {
        return $this->repository->remove($id);
    }

    public function getScheduledPosts()
    {
        return $this->repository->getScheduledPosts();
    }

    public function setPublishStatus($id)
    {
        $this->repository->updateItem($id, [
            'status' => PostStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }

    private function storeMedia($userId, $media)
    {
        if(!empty($media)) {
            return $this->storeFile($media, $this->mediaFolder);
        }

        $cacheKey = GenerateService::IMAGE_CACHE_KEY;
        $cachePath = Cache::get("{$cacheKey}-{$userId}");

        if ($cachePath) {
            $sourcePath = GenerateService::IMAGE_TEMP_FOLDER . '/' . $cachePath;

            $finalPath = StorageService::moveFile($sourcePath, $this->mediaFolder);

            Cache::forget("{$cacheKey}-{$userId}");

            return $finalPath;
        }

        return null;
    }

    public function getPostMedia(string $id): array
    {
        $data = $this->repository->findByField('id', $id);

        return $this->getMedia($data, "media_path", $this->mediaFolder);
    }

    public function getDispatchableScheduledPosts()
    {
        return $this->repository->getDispatchableScheduledPosts();
    }

    public function autoCreate(string $subject, array $accountIds): bool
    {
        $accounts = array_map(function ($accountId) {
            return $this->socialAccountRepository->findByField('id', $accountId)->toArray();
        }, $accountIds);


        try {
            DB::beginTransaction();
            $post = $this->repository->newItem([
                'subject' => $subject,
                'status' => PostStatus::PENDING
            ]);

            $this->repository->attachSocialAccounts($post->id, $accounts);

            $this->n8nService->publishPost($post->id, $subject, $accounts);

            DB::commit();
            event(new PostCreated($post));
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
        }
        return false;
    }

    public function verifyAutoCreate($data): bool
    {
        try {
            $media = Http::get($data["generated_media_url"])->body();
            $mediaName = $this->storeMedia($data["user_id"], $media);

            $this->update($data["post_id"], [
                'status' => PostStatus::PUBLISHED,
                'content' => $data["generated_content"],
                'tags' => $data["generated_tags"],
                'published_at' => now(),
                'media_path' => $mediaName,
                'n8n_execution_id' => $data["execution_id"],
            ]);
            return true;
        }catch (\Exception $exception){
            Log::error("Error in n8n callback for Post {$data["post_id"]}: " . $exception->getMessage(),[
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            $this->update($data["post_id"], [
                'status' => PostStatus::FAILED,
            ]);
            return false;
        }

    }
}
