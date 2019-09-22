<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\v1\CheckPermissions;

class AssignPermissionsToUserRequest extends FormRequest
{
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
