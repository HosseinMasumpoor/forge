<?php

namespace Modules\SocialSync\app\Services\SocialMediaProviders;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Socialite;
use Modules\SocialSync\app\Interfaces\SocialMediaProviderInterface;
use RuntimeException;


class InstagramProvider implements SocialMediaProviderInterface
{

    private const GRAPH_API_VERSION = 'v18.0';
    private const GRAPH_API_URL = 'https://graph.facebook.com';

    public function publish(string $text, string $mediaPath, array $credentials = []): string
    {
        return true;

        $this->validateCredentials($credentials);

        if (empty($mediaPath)) {
            throw new RuntimeException("Instagram requires an image/video to publish a post.");
        }

        $accessToken = $credentials['access_token'];
        $accountId = $credentials['instagram_account_id'];

        $imageUrl = $this->getPublicUrl($mediaPath);

        try {
            $containerId = $this->createMediaContainer($accountId, $imageUrl, $text, $accessToken);

            $mediaId = $this->publishMediaContainer($accountId, $containerId, $accessToken);

            return (string) $mediaId;

        } catch (\Exception $e) {
            Log::error("Instagram Publish Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Step 1: Create Container
     */
    private function createMediaContainer(string $accountId, string $imageUrl, string $caption, string $token): string
    {
        $endpoint = sprintf('%s/%s/%s/media', self::GRAPH_API_URL, self::GRAPH_API_VERSION, $accountId);

        $response = Http::post($endpoint, [
            'image_url' => $imageUrl,
            'caption'   => $caption,
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            throw new RuntimeException("Instagram Container Failed: " . $response->body());
        }

        return $response->json('id');
    }

    /**
     * Step 2: Publish Container
     */
    private function publishMediaContainer(string $accountId, string $containerId, string $token): string
    {
        $endpoint = sprintf('%s/%s/%s/media_publish', self::GRAPH_API_URL, self::GRAPH_API_VERSION, $accountId);

        $response = Http::post($endpoint, [
            'creation_id' => $containerId,
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            throw new RuntimeException("Instagram Publish Failed: " . $response->body());
        }

        return $response->json('id');
    }

    private function getPublicUrl(string $path): string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (!str_starts_with($path, 'http')) {
            $path = config('app.url') . $path;
        }

        return $path;
    }

    private function validateCredentials(array $credentials): void
    {
        if (empty($credentials['access_token'])) {
            throw new \InvalidArgumentException("Missing Instagram Access Token.");
        }
        if (empty($credentials['instagram_account_id'])) {
            throw new \InvalidArgumentException("Missing Instagram Account ID (Business ID).");
        }
    }

    public function connect()
    {
        return Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
    }

    public function verify(array $data): ?array
    {
        $socialiteUser = Socialite::driver('facebook')->stateless()->user();
        $longLivedToken = $this->getLongLivedToken($socialiteUser->token);
        $instagramAccounts = $this->getInstagramBusinessAccounts($longLivedToken);

        if(empty($instagramAccounts)){
            return null;
        }

        $selectedAccount = $instagramAccounts[0];

        return [
            'provider_id' => $selectedAccount['id'],
            'name' => $selectedAccount['username'],
            'access_token' => $longLivedToken,
            'refresh_token' => null,
            'token_expires_at' => now()->addDays(60),
            'meta' => [
                'facebook_user_id' => $socialiteUser->getId(),
                'facebook_page_id' => $selectedAccount['page_id'],
            ],
            'credentials' => [],
        ];
    }

    private function getLongLivedToken(string $shortToken): string
    {
        $response = Http::get("https://graph.facebook.com/v19.0/oauth/access_token", [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $shortToken,
        ]);

        if ($response->failed()) {
            throw new Exception("Failed to get long-lived token.");
        }

        return $response->json('access_token');
    }

    private function getInstagramBusinessAccounts(string $longLivedToken): array
    {
        $pagesResponse = Http::withToken($longLivedToken)
            ->get("https://graph.facebook.com/v19.0/me/accounts");

        $pages = $pagesResponse->json('data');
        $instagramAccounts = [];

        foreach ($pages as $page) {
            $instagramResponse = Http::withToken($page['access_token'])
            ->get("https://graph.facebook.com/v19.0/{$page['id']}", [
                'fields' => 'instagram_business_account',
            ]);

            $instagramData = $instagramResponse->json();

            if (isset($instagramData['instagram_business_account'])) {
                $instagramAccounts[] = [
                    'id' => $instagramData['instagram_business_account']['id'],
                    'username' => $instagramData['instagram_business_account']['username'],
                    'page_id' => $page['id'],
                ];
            }
        }

        return $instagramAccounts;
    }
}
