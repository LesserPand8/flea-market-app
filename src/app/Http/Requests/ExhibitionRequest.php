<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'required|max:255',
            'image' => 'required|mimes:jpeg,png',
            'category' => 'required|array',
            'category.*' => 'integer|exists:categories,id',
            'condition' => ['required', 'string', 'in:良好,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い'],
            'price' => 'required|integer|min:0',
            'brand' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'category.required' => 'カテゴリーを1つ以上選択してください',
            'category.array' => 'カテゴリーの形式が不正です',
            'category.*.integer' => 'カテゴリーIDが不正です',
            'category.*.exists' => '選択されたカテゴリーが存在しません',
            'condition.in' => '商品の状態は選択肢から選んでください',
            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '商品画像は.jpegまたは.png形式でアップロードしてください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
