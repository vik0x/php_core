<?php

namespace App\Http\Requests\v1;

class CreateUserRequest extends FormRequest
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
        $validation = [
            'username'      => ['alpha_dash'],
            'email'         => ['email:rfc,dns'],
            'first_name'    => ['alpha'],
            'middle_name'   => ['alpha'],
            'last_name'     => ['alpha'],
            'type'          => ['alpha'],
            'status'        => ['alpha'],
            'can_login'     => ['boolean']
        ];
        if (strtoupper($this->method()) === 'POST') {
            $validation = [
                'username'      => ['required', 'alpha_dash', 'unique:users,username'],
                'email'         => ['required', 'email:rfc,dns', 'unique:users,email'],
                'first_name'    => ['required', 'alpha'],
                'middle_name'   => ['alpha'],
                'last_name'     => ['required', 'alpha'],
                'password'      => ['required', 'confirmed'],
                'type'          => ['alpha'],
                'status'        => ['alpha'],
                'can_login'     => ['boolean'],
                'mobile_number' => ['string', 'min:10'],
            ];
        }
        return $validation;
    }
}
