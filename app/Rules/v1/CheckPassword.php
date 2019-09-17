<?php

namespace App\Rules\v1;

use Illuminate\Contracts\Validation\Rule;
use Hash;

class CheckPassword implements Rule
{
    protected $password;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(String $password)
    {
        $this->password = $password;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('passwords.invalid');
    }
}
