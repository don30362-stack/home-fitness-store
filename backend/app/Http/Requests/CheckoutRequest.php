<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'purchaser' => [
                'required',
                'array:name,phone,email',
            ],
            'purchaser.name' => [
                'required',
                'string',
                'max:50',
            ],
            'purchaser.phone' => [
                'required',
                'string',
                'regex:/^09[0-9]{8}$/',
            ],
            'purchaser.email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],

            'recipient' => [
                'required',
                'array:name,phone,district_id,address',
            ],
            'recipient.name' => [
                'required',
                'string',
                'max:50',
            ],
            'recipient.phone' => [
                'required',
                'string',
                'regex:/^09[0-9]{8}$/',
            ],
            'recipient.district_id' => [
                'required',
                'integer',
                'exists:districts,id',
            ],
            'recipient.address' => [
                'required',
                'string',
                'max:255',
            ],

            'shipping_method' => [
                'required',
                'string',
                Rule::in(['home_delivery']),
            ],
            'payment_method' => [
                'required',
                'string',
                Rule::in([
                    'cod',
                    'mock_credit_card',
                ]),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute為必填欄位。',
            'array' => ':attribute格式不正確。',
            'string' => ':attribute格式不正確。',
            'integer' => ':attribute格式不正確。',
            'max.string' => ':attribute不得超過 :max 個字元。',
            'email' => '請輸入正確的 Email。',
            'regex' => ':attribute格式不正確。',
            'exists' => '選擇的:attribute不存在。',
            'in' => '選擇的:attribute不正確。',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'purchaser' => '訂購人資料',
            'purchaser.name' => '訂購人姓名',
            'purchaser.phone' => '訂購人手機號碼',
            'purchaser.email' => '訂購人 Email',
            'recipient' => '收件人資料',
            'recipient.name' => '收件人姓名',
            'recipient.phone' => '收件人手機號碼',
            'recipient.district_id' => '行政區',
            'recipient.address' => '詳細地址',
            'shipping_method' => '配送方式',
            'payment_method' => '付款方式',
        ];
    }
}
