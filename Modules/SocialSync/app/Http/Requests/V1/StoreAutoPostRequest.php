<?php

namespace Modules\SocialSync\app\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SocialSync\app\Rules\UserOwnsSocialAccounts;

class StoreAutoPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'subject' => ['required', 'string', 'max:255'],
            'social_account_ids' => ['required', 'array', 'min:1', new UserOwnsSocialAccounts($userId)],
            'social_account_ids.*' => ['required', 'integer', 'exists:social_accounts,id']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}

