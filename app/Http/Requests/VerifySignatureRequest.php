<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifySignatureRequest extends FormRequest
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
            'address' => ['required', 'string', 'regex:/^0x[a-fA-F0-9]{40}$/'],
            'signature' => [
                'required',
                'string',
                'regex:/^0x([A-Fa-f0-9]{130})$/',
            ],
        ];
    }
}
