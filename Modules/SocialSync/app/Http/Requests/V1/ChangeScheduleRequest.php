<?php

namespace Modules\SocialSync\app\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\SocialSync\app\Rules\ScheduleChangeablePost;
use Modules\SocialSync\app\Rules\UserOwnsSocialAccounts;

class ChangeScheduleRequest extends FormRequest
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
            'id' => ['required', 'exists:posts,id', new ScheduleChangeablePost($userId)],
            'scheduled_at' => ['required', 'date', 'after_or_equal:now'],
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

