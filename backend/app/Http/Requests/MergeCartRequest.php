<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeCartRequest extends FormRequest
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
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items.*.product_variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => '訪客購物車資料不能為空。',
            'items.array' => '訪客購物車資料格式錯誤。',
            'items.min' => '訪客購物車至少需要一筆商品。',

            'items.*.product_id.required' => '每筆購物車資料都必須包含商品。',
            'items.*.product_id.integer' => '商品資料格式錯誤。',
            'items.*.product_id.exists' => '購物車內含有不存在的商品。',

            'items.*.product_variant_id.integer' => '商品規格格式錯誤。',
            'items.*.product_variant_id.exists' => '購物車內含有不存在的商品規格。',

            'items.*.quantity.required' => '每筆購物車資料都必須包含購買數量。',
            'items.*.quantity.integer' => '購買數量必須是整數。',
            'items.*.quantity.min' => '購買數量至少為 1。',
        ];
    }
}
