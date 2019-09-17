<?php

namespace App\Http\Requests\v1;

use Spatie\Permission\Models\Role;

class AlterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $superAdmin = Role::findByName(config('settings.user.rootRole'));
        if (!$this->user()->hasRole($superAdmin) && $this->has('role_id')) {
            return $superAdmin->id !== (int)$this->input('role_id');
        }
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
            'role_id'       => ['required', 'exists:roles,id'],
            'username'      => ['required', 'alpha_dash', 'unique:users,username'],
            'email'         => ['required', 'email:rfc,dns', 'unique:users,email'],
            'first_name'    => ['required', 'string'],
            'middle_name'   => ['string'],
            'last_name'     => ['required', 'string'],
            'password'      => ['required', 'confirmed'],
            'status'        => ['alpha'],
            'can_login'     => ['boolean'],
            'mobile_number' => ['string', 'min:10'],
        ];
        if ($this->isMethod('put')) {
            $user = $this->route()->parameter('user');
            $validation = [
                'username'      => ['alpha_dash', 'unique:users,username,' . $user->id],
                'email'         => ['email:rfc,dns', 'unique:users,email,' . $user->id],
                'first_name'    => ['string'],
                'middle_name'   => ['string'],
                'last_name'     => ['string'],
                'status'        => ['alpha'],
                'can_login'     => ['boolean']
            ];
        }
        return $validation;
    }
}
