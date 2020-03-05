<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest.
 */
class UpdateUserRequest extends FormRequest
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
        $rules = [
            'user_id' => 'required|numeric',
            'email' => 'required|email|max:255|'.Rule::unique('users')->ignore($this->get('user_id')),
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|'.Rule::unique('users')->ignore($this->get('user_id')),
        ];

        if ('teacher' == $this->get('user_role')) {
            $rules['department_id'] = 'required|numeric';
        }

        return $rules;
    }
}
