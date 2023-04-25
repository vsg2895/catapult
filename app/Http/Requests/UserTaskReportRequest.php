<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserTaskReportRequest extends FormRequest
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
            'text' => 'required_without:files|string|min:1',
            'files' => 'required_without:text|array|max:3',
            'files.*' => 'mimes:jpg,jpeg,png,pdf|min:10|max:10000',
        ];
    }
}
