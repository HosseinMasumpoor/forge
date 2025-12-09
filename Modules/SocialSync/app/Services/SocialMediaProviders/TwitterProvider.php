<?php

namespace Modules\SocialSync\app\Services\SocialMediaProviders;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Modules\SocialSync\app\Interfaces\SocialMediaProviderInterface;
use RuntimeException;

class TwitterProvider implements SocialMediaProviderInterface
{
    public function publish(string $text, ?string $mediaPath, array $credentials = []): ?string
    {
        return true;
        $this->validateCredentials($credentials);

        $connection = new TwitterOAuth(
            config('socialsync.social.twitter.consumer_key'),
            config('socialsync.social.twitter.consumer_secret'),
            $credentials['access_token'],
            $credentials['access_token_secret']
        );

        $connection->setTimeouts(10, 30);

        try {
            $mediaId = null;

            if ($mediaPath) {
                $mediaId = $this->uploadMedia($connection, $mediaPath);
            }

            $connection->setApiVersion('2');

            $payload = ['text' => $text];

            if ($mediaId) {
                $payload['media'] = ['media_ids' => [(string)$mediaId]];
            }

            $result = $connection->post('tweets', $payload, true); // true = json body

            if ($connection->getLastHttpCode() !== 201) {
                throw new RuntimeException("Twitter API Error: " . json_encode($result));
            }

            return (string) $result->data->id;

        } catch (\Throwable $e) {
            Log::error("Twitter Publish Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function uploadMedia(TwitterOAuth $connection, string $path): string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Media file not found at: $path");
        }

        $connection->setApiVersion('1.1');

        $media = $connection->upload('media/upload', ['media' => $path]);

        if (isset($media->errors)) {
            throw new RuntimeException("Media Upload Error: " . json_encode($media->errors));
        }

        return $media->media_id_string;
    }

    private function validateCredentials(array $credentials): void
    {
        $requiredKeys = ['consumer_key', 'consumer_secret', 'access_token', 'access_token_secret'];

        foreach ($requiredKeys as $key) {
            if (empty($credentials[$key])) {
                throw new \InvalidArgumentException("Missing Twitter credential: $key");
            }
        }
    }

    public function connect()
    {
        return Socialite::driver('twitter')->stateless()->redirect()->getTargetUrl();;
    }

    public function verify(array $data) : ?array
    {
        if(!$data["code"]){
            return null;
        }

        $socialUser = Socialite::driver('twitter')->stateless()->userFromToken($data['code']);

        return [
            'provider_id' => $socialUser->getId(),
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'access_token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            'meta' => [
                'nickname' => $socialUser->getNickname(),
                'avatar' => $socialUser->getAvatar(),
                'email' => $socialUser->getEmail(),
            ],
            'credentials' => [],
        ];
    }
}
