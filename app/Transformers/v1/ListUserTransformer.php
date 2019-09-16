<?php

namespace App\Transformers\v1;

use League\Fractal\TransformerAbstract;
use App\Models\v1\User;

class ListUserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'            => $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
            'mobile_number' => $user->mobile_number,
            'first_name'    => $user->first_name,
            'middle_name'   => $user->middle_name,
            'last_name'     => $user->last_name,
            'type'          => $user->type,
            'status'        => $user->status,
            'can_login'     => $user->can_login
        ];
    }
}
