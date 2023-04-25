<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'name' => 'required|string|min:1',
            'email' => [
                'email',
                Rule::unique('users')->ignore($this->user()->id, 'id'),
            ],
            'avatar' => 'nullable|mimes:jpg,jpeg,png|max:10000',
            'password' => ['confirmed', Password::defaults()],
            'country_id' => 'integer|exists:countries,id',
            'languages' => 'array',
            'languages.*.id' => 'integer|exists:user_languages,id',
            'languages.*.language_id' => 'required|integer|exists:languages,id',
            'skills' => 'array',
            'skills.*.id' => 'integer|exists:user_skills,id',
            'skills.*.skill_id' => 'required|integer|exists:skills,id',
            'activities' => 'array',
            'activities.*.id' => 'integer|exists:user_activities,id',
            'activities.*.activity_id' => 'required|integer|exists:activities,id',
            'activity_links' => 'array',
            'activity_links.*.id' => 'integer|exists:user_activity_links,id',
            'activity_links.*.content' => 'required|string|min:1',
            'activity_links.*.activity_link_id' => 'required|integer|exists:activity_links,id',
        ];
    }
}
