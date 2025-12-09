<?php

namespace Modules\SocialSync\app\Services;

use Modules\Core\Services\APIService;

class N8NService
{
    private string $n8nUrl;

    public function __construct(protected APIService $apiService)
    {
        $this->n8nUrl = config('socialsync.n8n.url', '');
    }

    public function publishPost(string $postId, string $subject, array $accounts): bool
    {
        $url = $this->n8nUrl . '/' . "create-post";

        $response = $this->apiService->post($url, [
            'post_id' => $postId,
            'subject' => $subject,
            'accounts' => $accounts,
            'callback_url' => route(''),
            'secret_key' => config('socialsync.n8n.secret_key'),
        ]);

        return $response->successful();
    }
}
