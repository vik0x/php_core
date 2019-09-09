<?php

namespace App\Models\v1;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    static public function findByUsername(String $username)
    {
        $user = User::where('username', $username)->first();
        if ($user === null) {
            $user = User::where('email', $username)->first();
        }
        return $user;
    }

    static public function getUserByResetToken(String $token)
    {
        $response = null;
        $user = User::where('reset_password_token', $token)->first();
        if ($user !== null) {
            $now = Carbon::now();
            $expirationDate = Carbon::parse($user->token_expiration_date);
            if ($now < $expirationDate) {
                $response = $user;
            }
        }
        return $response;
    }
}