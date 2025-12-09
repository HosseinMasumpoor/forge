<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StorageService
{
    public static function addFileStorage($path, $file): bool
    {
        return Storage::put($path, $file);
    }

    public static function removeFileStorage($path): bool
    {
        return Storage::delete($path);
    }

    public static function getFileStorage($path): ?string
    {
        return Storage::get($path);
    }

    public static function downloadFileStorage($path): StreamedResponse
    {
        return Storage::download($path);
    }

    public static function moveFile(string $sourcePath, string $destinationPath): bool
    {
        return Storage::move($sourcePath, $destinationPath);
    }
}
