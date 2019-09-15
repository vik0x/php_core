<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\v1\CheckPassword;

class AltereRoleRequest extends FormRequest
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
            'user_password' => ['required', new CheckPassword($this->user()->password)],
            'role' => ['required', 'string', 'min:3', 'unique:roles,name'],
        ];
        if ($this->isMethod('put')) {
            $role = $this->route()->parameter('role');
            $rules['role'] = ['required', 'string', 'min:3', 'unique:roles,name,' . $role->id];
        }
        return $rules;
    }
}
