<?php

namespace Modules\User\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Rules\MobileRule;

class VerifyOTPRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', new MobileRule],
            'code' => ['required', 'string', 'size:6']
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
