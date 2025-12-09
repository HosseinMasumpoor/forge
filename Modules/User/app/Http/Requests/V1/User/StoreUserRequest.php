<?php

namespace Modules\User\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Enums\UserStatus;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:190',
            'status' => [Rule::in(UserStatus::getValues())],
            'email' => 'nullable|string|max:150',
            'mobile' => 'required|string|digits:11',
            'password' => 'required|string|min:8|max:32',
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
