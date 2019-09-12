<?php

namespace App\Http\Requests\v1;

class UserCreateRequest extends FormRequest
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
            'username'      => ['required', 'alpha_dash', 'unique:users,username'],
            'email'         => ['required', 'email:rfc,dns', 'unique:users,email'],
            'first_name'    => ['required', 'alpha'],
            'middle_name'   => ['required', 'alpha'],
            'last_name'     => ['required', 'alpha'],
            'type'          => ['alpha'],
            'status'        => ['alpha'],
            'can_login'     => ['boolean']
        ];
    }
}
