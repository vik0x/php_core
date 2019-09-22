<?php

namespace App\Http\Requests\v1;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'string'],
            'username' => ['required', 'string'],
        ];
    }
}
