<?php

namespace Modules\User\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class ChangeEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = auth('user')->user()->id;
        return [
            'email' => ["required", "email", "unique:users,email,$userId"],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
