<?php

namespace App\Http\Requests\v1;

class LoginRequest extends FormRequest
{
    
    protected $rules = [
        'password' => ['required', 'string'],
        'username' => ['required', 'string'],
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }

    /**
     * Get the params request.
     *
     * @return array
     */

    public function allValid()
    {

        $request = collect($this->only(array_keys($this->rules())));
        
        return $request->merge([
            'clientId'=> env('PASSWORD_CLIENT_ID'),
            'secret'=> env('PASSWORD_CLIENT_SECRET')
        ])->toArray();
    }
}
