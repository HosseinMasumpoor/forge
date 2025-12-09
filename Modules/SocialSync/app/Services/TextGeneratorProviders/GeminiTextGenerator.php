<?php

namespace Modules\SocialSync\app\Services\TextGeneratorProviders;

use Illuminate\Support\Facades\Log;
use Modules\Core\Services\APIService;
use Modules\SocialSync\app\Interfaces\TextGeneratorInterface;

class GeminiTextGenerator implements TextGeneratorInterface
{
    public function __construct(protected APIService $apiService)
    {
        $token = config('socialsync.generator.gemeni.token') ?? null;
        $baseUrl = config('socialsync.generator.gemeni.base_url') ?? '';

        $this->apiService->config($token, $baseUrl);
    }

    public function generate(string $subject, array $settings = []): ?string
    {
        $prompt = "Write a compelling and engaging social media post about: '{$subject}'. Include exactly 8 relevant hashtags at the end.";

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]],
            ],

            'config' => [
                'temperature' => $settings['temperature'] ?? 0.7,
                'maxOutputTokens' => $settings['max_tokens'] ?? 2048,
            ],
        ];

        try {
            $response = $this->apiService->post('v1beta/models/gemini-2.5-flash:generateContent', $payload);
            if ($response->failed()) {
                Log::error("Gemini API Error", ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

                $text = $response->json('candidates.0.content.parts.0.text');

                if (empty($text)) {
                    return null;
                }

                return trim($text);

            } catch (\Exception $e) {
                Log::error("Gemini API Exception: " . $e->getMessage());
                return null;
        }
    }
}
