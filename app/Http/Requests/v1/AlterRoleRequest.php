<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\v1\CheckPassword;

class AlterRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //The body parameter in the request for role creation/edition must not be equal than the model for implicit binding issues when trying to edit (PUT)
        $role = $this->route()->parameter('role');
        $check = new CheckPassword($this->user()->password);
        if ($role) {
            return $check->passes('password', $this->user_password) && $this->role->name !== config('settings.user.rootRole');
        }
        return $check->passes('password', $this->user_password) && $this->role_name !== config('settings.user.rootRole');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'role_name' => ['required', 'string', 'min:3', 'unique:roles,name'],
        ];
        if ($this->isMethod('put')) {
            $role = $this->route()->parameter('role');
            $rules['role_name'] = ['required', 'string', 'min:3', 'unique:roles,name,' . $role->id];
        }
        return $rules;
    }
}
