<?php

namespace App\Http\Requests\v1;

class ProfilePictureRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $mimes = config('settings.user.profile_picture.ext');
        $max = config('settings.user.profile_picture.max_size');
        return [
            'profile_image' => ['required', 'mimes:' . $mimes, 'max:' . $max]
        ];
    }
}
