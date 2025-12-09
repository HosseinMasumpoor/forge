<?php

namespace Modules\SocialSync\app\Services\ImageGeneratorProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Core\Services\APIService;
use Modules\Core\Traits\Media;
use Modules\SocialSync\app\Interfaces\ImageGeneratorInterface;

class BananaImageGenerator implements ImageGeneratorInterface
{
    use Media;

    protected string $imageFolder = 'post/images';

    public function __construct(protected APIService $apiService)
    {
        $token = config('socialsync.generator.banana.token') ?? '';
        $baseUrl = config('socialsync.generator.banana.base_url') ?? '';

        $this->apiService->config($token, $baseUrl);
    }

    public function generate(string $subject, array $settings = []): ?string
    {
        $payload = [
            'modelKey' => config('socialsync.generator.banana.model_key'),
            'modelInputs' => [
                'prompt' => $subject,
                'width' => 512,
                'height' => 512,
                'num_inference_steps' => 50
            ],
        ];

        try {
            $response = $this->apiService->post('v1/generate', $payload);

            if ($response->failed()) {
                Log::error("Banana API Error", ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $imageUrl = $response->json('image_url');
            if (empty($imageUrl)) {
                return null;
            }

            return $imageUrl;

        } catch (\Exception $e) {
            Log::error("Banana Image Generation Exception: " . $e->getMessage());
            return null;
        }
    }
}
