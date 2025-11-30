<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
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
            'content' => ['required', 'string', 'max:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'Todoを入力してください',
            'content.string' => 'Todoを文字列で入力してください',
            'content.max' => 'Todoは20文字以内で入力してください',
            'content.regex' => 'Todoに数字は使用できません',
            'category_id.required' => 'カテゴリを選択してください',
            'category_id.exists' => 'カテゴリが存在しません',
            'due_date.date' => '期限日を選択してください',
        ];
    }
}
