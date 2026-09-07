<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreUserAddressRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'district_id' => [
                'required',
                'integer',
                'exists:districts,id',
            ],
            'label' => [
                'required',
                'string',
                'max:30',
            ],
            'recipient_name' => [
                'required',
                'string',
                'max:50',
            ],
            'recipient_phone' => [
                'required',
                'string',
                'regex:/^09[0-9]{8}$/',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'district_id.exists' => '選擇的行政區不存在。',
            'recipient_phone.regex' => '收件人電話必須是 09 開頭的 10 位數字。',
        ];
    }
}
