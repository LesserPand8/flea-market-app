<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradingChatRequest extends FormRequest
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
            'item_id' => 'required|integer',
            'message_text' => 'required|string|max:400',
            'chat_image' => 'nullable|mimes:jpeg,png',
        ];
    }

    /**
     * Get the custom messages that apply to the request validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'message_text.required' => '本文を入力してください',
            'message_text.max' => '本文は400文字以内で入力してください',
            'chat_image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
