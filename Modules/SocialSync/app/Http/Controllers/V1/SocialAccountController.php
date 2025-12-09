<?php

namespace Modules\SocialSync\app\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Core\Http\Controllers\CoreController;
use Modules\SocialSync\app\Http\Requests\V1\UpdateSocialAccountRequest;
use Modules\SocialSync\app\Models\SocialAccount;
use Modules\SocialSync\app\Services\SocialMediaProviderResolver;
use Modules\SocialSync\app\Services\TextGeneratorProviders\SocialAccountService;

class SocialAccountController extends CoreController
{
    use AuthorizesRequests;

    public function __construct(
        private readonly SocialAccountService $service
    ) {}

    /**
     * Display a listing of the user's social accounts.
     */
    public function index(Request $request)
    {
        $userId = auth('user')->id();
        $itemsPerPage = $request->input('itemsPerPage', config('app.default_paginate_number', 10));

        $data = $this->service->getByUserId($userId)
            ->with('user')
            ->paginate($itemsPerPage);

        return successResponse($data);
    }

    public function smallList()
    {
        $userId = auth('user')->id();
        $data = $this->service->getByUserId($userId)->get();
        return successResponse($data);
    }

    /**
     * Update the specified social account (only name field).
     */
    public function update(UpdateSocialAccountRequest $request, string $id)
    {
        $socialAccount = $this->service->getById($id);
        if (!$socialAccount) {
            return failedResponse(__('socialsync::messages.social_account.not_found'), 404);
        }

        $this->authorize('update', $socialAccount);

        $data = $request->only(['name']);
        $result = $this->service->update($id, $data);

        if (!$result) {
            return failedResponse(__('socialsync::messages.social_account.update_error'));
        }

        return successResponse([], __('socialsync::messages.social_account.updated_successfully'));
    }

    /**
     * Remove the specified social account (soft delete).
     */
    public function destroy(string $id)
    {
        $socialAccount = $this->service->getById($id);

        if (!$socialAccount) {
            return failedResponse(__('socialsync::messages.social_account.not_found'), 404);
        }

        $this->authorize('delete', $socialAccount);

        $result = $this->service->delete($id);

        if (!$result) {
            return failedResponse(__('socialsync::messages.social_account.delete_error'));
        }

        return successResponse(null, __('socialsync::messages.social_account.deleted_successfully'));
    }

    public function redirectToProvider(string $provider)
    {
        $provider = SocialMediaProviderResolver::resolve($provider);
        $redirectUrl = $provider->connect();
        return successResponse(['redirectUrl' => $redirectUrl]);
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        $request->validate([
            'code' => 'nullable|string',
        ]);

        $providerDriver = SocialMediaProviderResolver::resolve($provider);
        $data = $providerDriver->verify($request->all());
        if (!$data) {
            return failedResponse(__('core::messages.error'));
        }

        $item = $this->service->updateOrCreate([
            'user_id' => auth('user')->id(),
            'provider' => $provider,
            'provider_id' => $data['provider_id'],
        ], [
            'name' => $data['name'],
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_expires_at' => $data['token_expires_at'],
            'meta' => $data['meta'],
            'credentials' => $data['credentials'],
        ]);

        if (!$item) {
            return failedResponse(__('core::messages.error'));
        }

        return successResponse($item, __('core::messages.success'));
    }

}
