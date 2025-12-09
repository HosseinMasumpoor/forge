<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Services\StorageService;

trait Media
{
    private function storeFile($file, $folder)
    {
        $fileName = $file->hashName();
        StorageService::addFileStorage($folder, $file);
        return $fileName;
    }

    private function deleteFile($data, $fieldName, $folder): bool
    {
        if(isset($data[$fieldName]) && $data[$fieldName]){
            return StorageService::removeFileStorage("$folder/$data[$fieldName]");
        }
        return false;
    }

    private function getMedia($data, $fieldName, $folder ): array
    {
        $path = $folder . '/' . $data->$fieldName;
        return $this->getFileAndType($path);
    }

    private function getFileAndType($path): array
    {
        $file = StorageService::getFileStorage($path);
        $type = Storage::mimeType($path);

        return compact('file', 'type');
    }
}
