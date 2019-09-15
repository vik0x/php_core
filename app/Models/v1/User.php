<?php

namespace App\Models\v1;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password', 'email', 'mobile_number', 'first_name', 'middle_name', 'last_name', 'type', 'status', 'can_login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password', 'remember_token', 'reset_password_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function findByUsername(String $username)
    {
        $user = User::where('username', $username)->first();
        if ($user === null) {
            $user = User::where('email', $username)->first();
        }
        return $user;
    }

    public static function getUserByResetToken(String $token)
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
