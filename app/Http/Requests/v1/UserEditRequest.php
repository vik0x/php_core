<?php

namespace App\Http\Requests\v1;

class UserEditRequest extends FormRequest
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
            'username'      => ['alpha_dash'],
            'email'         => ['email:rfc,dns'],
            'first_name'    => ['alpha'],
            'middle_name'   => ['alpha'],
            'last_name'     => ['alpha'],
            'type'          => ['alpha'],
            'status'        => ['alpha'],
            'can_login'     => ['boolean']
        ];
    }
}
