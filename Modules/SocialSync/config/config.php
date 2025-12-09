<?php

return [
    'name' => 'SocialSync',
    'generator' => [
        'banana' => [
            'token' => env('GEMINI_API_TOKEN', ''),
            'base_url' => env('GEMINI_API_BASE_URL', ''),
            'model_key' => env('GEMINI_MODEL_KEY', ''),
        ],
        'gemeni' => [
            'token' => env('GEMINI_API_TOKEN', ''),
            'base_url' => env('GEMINI_API_BASE_URL', '')
        ]
    ],
    'social' => [
        'twitter' => [
            'consumer_secret' => env('TWITTER_CONSUMER_SECRET', ''),
            'consumer_key'    => env('TWITTER_CONSUMER_KEY', '')
        ]
    ],
    'n8n' => [
        'url' => env('N8N_URL', ''),
        'secret_key' => env('N8N_SECRET_KEY', ''),
    ]
];
