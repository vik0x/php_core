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
        $role = $this->route()->parameter('role');
        $check = new CheckPassword($this->user()->password);
        if ($this->isMethod('post')) {
            return $check->passes('password', $this->user_password);
        }
        return $check->passes('password', $this->user_password) && $role->name !== config('settings.user.rootRole');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'role' => ['required', 'string', 'min:3', 'unique:roles,name'],
        ];
        if ($this->isMethod('put')) {
            $role = $this->route()->parameter('role');
            $rules['role'] = ['required', 'string', 'min:3', 'unique:roles,name,' . $role->id];
        }
        return $rules;
    }
}
