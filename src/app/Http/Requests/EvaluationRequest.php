<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
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
            'evaluation_score' => ['required', 'integer', 'min:1', 'max:5'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'evaluation_score.required' => '評価を選択してください。',
            'evaluation_score.integer' => '評価は1〜5で選択してください。',
            'evaluation_score.min' => '評価は1〜5で選択してください。',
            'evaluation_score.max' => '評価は1〜5で選択してください。',
        ];
    }
}
