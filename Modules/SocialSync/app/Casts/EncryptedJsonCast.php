<?php

namespace Modules\SocialSync\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class EncryptedJsonCast implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return [];
        }

        try {
            $decryptedString = Crypt::decryptString($value);

            return json_decode($decryptedString, true);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt credentials for social account: " . $model->id, ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException('The credentials attribute must be an array.');
        }

        $jsonString = json_encode($value);

        return Crypt::encryptString($jsonString);
    }
}
