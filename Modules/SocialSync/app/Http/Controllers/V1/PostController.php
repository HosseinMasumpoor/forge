<?php

namespace Modules\SocialSync\app\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\SocialSync\app\Http\Requests\V1\ChangeScheduleRequest;
use Modules\SocialSync\app\Http\Requests\V1\N8NCallbackRequest;
use Modules\SocialSync\app\Http\Requests\V1\StoreAutoPostRequest;
use Modules\SocialSync\app\Http\Requests\V1\StorePostRequest;
use Modules\SocialSync\app\Models\Post;
use Modules\SocialSync\app\Services\PostService;

class PostController extends CoreController
{
    public function __construct(
        private readonly PostService $service
    ) {}

    /**
     * Display a listing of the user's posts.
     */
    public function index(Request $request)
    {
        $userId = auth('user')->id();

        $itemsPerPage = $request->input('itemsPerPage', config('app.default_paginate_number', 10));

        $data = $this->service->getByUserId($userId)
            ->with(['socialAccounts', 'user'])
            ->paginate($itemsPerPage);

        return successResponse($data);
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request)
    {
        $user = auth('user')->user();

        if (!$user->can('create', Post::class)) {
            return failedResponse(__('socialsync::messages.post.limit_exceeded'), 403);
        }

        $userId = $user->id;
        $socialAccountIds = $request->input('social_account_ids');

        $postData = [
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
            'media_path' => $request->file('media_path'),
            'tags' => $request->input('tags'),
            'scheduled_at' => $request->input('scheduled_at') ? now()->parse($request->input('scheduled_at')) : null,
        ];

        $result = $this->service->createWithAccounts($userId, $postData, $socialAccountIds);
        if(!$result) {
            return failedResponse(__('socialsync::messages.post.error'));
        }
        return successResponse([], __('socialsync::messages.post.success'), 201);

    }

    /**
     * Display the specified post.
     */
    public function show(string $id)
    {
        $user = auth('user')->user();

        $socialAccount = $this->service->getById($id);

        if (!$socialAccount) {
            return failedResponse(__('socialsync::messages.social_account.not_found'), 404);
        }

        if (!$user->can('view', Post::class)) {
            return failedResponse(__('socialsync::messages.post.not_found'), 403);
        }

        $post = $this->service->getById($id);

        if (!$post) {
            return failedResponse(__('socialsync::messages.post.not_found'), 404);
        }

        return successResponse($post->load(['socialAccounts', 'user']));
    }

    /**
     * Remove the specified post.
     */
    public function destroy(string $id)
    {
        $user = auth('user')->user();

        $socialAccount = $this->service->getById($id);

        if (!$socialAccount) {
            return failedResponse(__('socialsync::messages.social_account.not_found'), 404);
        }

        if (!$user->can('delete', $socialAccount)) {
            return failedResponse(__('socialsync::messages.post.not_found'), 403);
        }

        $this->service->delete($id);

        return successResponse([], __('socialsync::messages.post.deleted_successfully'));
    }

    public function latest(Request $request)
    {
        $limit = $request->input('count', 5);

        $userId = auth('user')->id();

        $data = $this->service->getByUserId($userId)
            ->with(['socialAccounts', 'user'])
            ->latest()
            ->limit($limit)
            ->get();

        return successResponse($data);
    }

    public function getMedia($id)
    {
        $result = $this->service->getPostMedia($id);
        return fileResponse($result);
    }

    public function getSchedule()
    {
        $result = $this->service->getScheduledPosts()->oldest()->get();
        if($result){
            return successResponse($result);
        }
        return failedResponse(__('core::messages.error'));
    }

    public function changeSchedule(ChangeScheduleRequest $request)
    {
        $data = $request->validated();
        $result = $this->service->update($data['id'], ['scheduled_at' =>$data['scheduled_at']]);

        if($result){
            return successResponse([], __('socialsync::messages.post.updated_successfully'));
        }
        return failedResponse(__('socialsync::messages.post.error'));
    }

    public function autoCreate(StoreAutoPostRequest $request)
    {
        $data = $request->validated();
        $result = $this->service->autoCreate($data["subject"], $data["social_account_ids"]);
        if($result){
            return successResponse([], __('socialsync::messages.post.success'));
        }
        return failedResponse(__('socialsync::messages.post.error'));
    }

    public function n8nCallback(N8NCallbackRequest $request)
    {
        $data = $request->validated();
        $this->service->verifyAutoCreate($data);
    }
}

