<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserTaskListRequest extends FormRequest
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
            'status' => 'in:done,rejected,overdue,returned,in_progress,waiting_for_review',
            'search' => 'string|min:1',
            'per_page' => 'integer|min:1',
            'order_by_deadline' => 'required|in:asc,desc',
        ];
    }
}
