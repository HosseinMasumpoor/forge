<?php

namespace Modules\Order\app\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\app\Enums\TransactionGateway;

class OrderStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'gateway' => ['required', Rule::in(TransactionGateway::getValues())],
            'offer_id' => 'required|exists:subscription_offers,id',
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
