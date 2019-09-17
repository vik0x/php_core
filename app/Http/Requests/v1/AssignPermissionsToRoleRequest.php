<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\v1\CheckPermissions;
use App\Rules\v1\CheckPassword;

class AssignPermissionsToRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $check = new CheckPassword($this->user()->password);
        return $check->passes('password', $this->user_password);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permissions' => ['required', 'array', new CheckPermissions($this->user())]
        ];
    }
}
