<?php

namespace App\Http\Requests\v1;

use Illuminate\Validation\Factory as ValidationFactory;

class ProfilePictureRequest extends FormRequest
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
        $mimes = config('settings.user.profile_picture.ext');
        $max = config('settings.user.profile_picture.max_size');
        return [
            'profile_image' => ['required', 'mimes:' . $mimes, 'max:' . $max]
        ];
    }
}
