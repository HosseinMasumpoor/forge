<?php

namespace Modules\SocialSync\app\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Services\StorageService;
use Modules\Core\Traits\Media;
use Modules\SocialSync\app\Interfaces\ImageGeneratorInterface;
use Modules\SocialSync\app\Interfaces\TextGeneratorInterface;

class GenerateService
{
    use Media;
    const IMAGE_CACHE_KEY = "ai-generated-image";

    const IMAGE_TEMP_FOLDER = "ai/media/images";

    public function __construct(protected ImageGeneratorInterface $imageGenerator, protected TextGeneratorInterface $textGenerator) {}

    public function textGenerate(string $subject): ?string
    {
        return $this->textGenerator->generate($subject);
    }

    public function imageGenerate(string $subject, string $userId): ?string
    {
        $imageUrl = $this->imageGenerator->generate($subject);
        $image = Http::get($imageUrl)->body();
        $fileName = StorageService::addFileStorage(self::IMAGE_TEMP_FOLDER, $image);
        $key = self::IMAGE_CACHE_KEY;
        Cache::set("{$key}-{$userId}", $fileName);
        return $fileName;
    }

    public function getGeneratedImage($userId)
    {
        $key = self::IMAGE_CACHE_KEY;
        $fileName = Cache::get("{$key}-{$userId}");

        if (!$fileName) {
            return null;
        }

        $path = self::IMAGE_TEMP_FOLDER . '/' . $fileName;
        StorageService::getFileStorage($path);
        $type = Storage::mimeType($path);
        return compact('fileName', 'type');
    }
}
