<?php

namespace Modules\SocialSync\app\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class N8NCallbackRequest extends FormRequest
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

        return [
            'post_id' => ['required', 'exists:posts,id'],
            'generated_content' => ['required', 'string'],
            'generated_tags' => ['required', 'string'],
            'generated_media_url' => ['required', 'url'],
            'execution_id' => ['nullable', 'string'],
            'publish_status' => ['required', 'boolean'],
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

