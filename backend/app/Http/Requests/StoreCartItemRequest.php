<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
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
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'product_variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id'
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => '請選擇商品。',
            'product_id.integer' => '商品資料格式錯誤。',
            'product_id.exists' => '商品不存在。',

            'product_variant_id.integer' => '商品規格格式錯誤。',
            'product_variant_id.exists' => '商品規格不存在。',

            'quantity.required' => '請輸入購買數量。',
            'quantity.integer' => '購買數量必須是整數。',
            'quantity.min' => '購買數量至少為 1。',
        ];
    }
}
