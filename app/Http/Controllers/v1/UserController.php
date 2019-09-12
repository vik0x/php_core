<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ForgotPasswordRequest;
use App\Http\Requests\v1\ResetPasswordRequest;
use App\Http\Requests\v1\UserEditRequest;
use App\Http\Requests\v1\UserCreateRequest;
use App\Mail\RecoverPassword;
use App\Models\v1\User;
use App\Traits\Mail as MailTrait;

class UserController extends Controller
{
   use MailTrait;
    /**
     * Restore the forgotten password.
     *
     * @param  App\Http\Requests\v1\ForgotPasswordRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * return 200 when the email was sent or 204 if the user was not found
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $username = $request->input('username');
        $user = User::findByUsername($username);

        $message = 'mailing.user_to_recover_password_not_found';
        $msgAttributes = ['username' => $username];

        if ($user !== null) {
            $email = $this->maskEmail($user->email, 2);
            $message = 'mailing.email_to_recover_password_was_send';
            $msgAttributes = ['email' => $email];
            $user->reset_password_token = bin2hex(random_bytes(32));
            $user->token_expiration_date = Carbon::now()->add(config('settings.user.expiration_days'), 'day');
            $user->save();
            $token = Crypt::encryptString($user->reset_password_token);
            Mail::to($user)->send(new RecoverPassword($token));
        }

        return response()->json([
            'message' => trans($message, $msgAttributes)
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $httpCode = 404;
        $message = 'validation.reset_password_invalid_token';
        extract($request->only('token', 'password'));
        $token = Crypt::decryptString($token);
        $user = User::getUserByResetToken($token);
        if ($user !== null) {
            $message = 'success';
            $httpCode = 200;
            $user->password = bcrypt($password);
            $user->reset_password_token = null;
            $user->save();
        }
        return response()->json([
            'message' => trans($message)
        ], $httpCode);
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        foreach(User::all() as $user){
            $collection[] = array(
                "type"  => "user",
                "id"    => $user->id,
                "links" => [
                    "self"  => url('/users/'. $user->id)
                ],
                "attributes" => [
                    $user
                ]
            );
        }
        
        return response()->json([
            'links' => [],
            "data"  => $collection
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserCreateRequest $request, User $user)
    {
        $user->fill($request->input())->save();

        return response()->json([
            'data' => [
                [
                    "type"      => "user",
                    "id"        => $user->id,
                    "attributes"=> [
                        $user
                    ],
                    "links"     => [
                        "self"  => url('/users/'. $user->id)
                    ]
                ]
            ],
            'status' => 'sucess',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'data' => [
                [
                    "type"      => "user",
                    "id"        => $user->id,
                    "attributes"=> [
                        $user
                    ],
                    "links"     => [
                        "self"  => url('/users/'. $user->id)
                    ]
                ]
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserEditRequest $request, User $user)
    {
        $user->fill($request->input())->update();

        return response()->json([
            'data' => [
                [
                    "type"      => "user",
                    "id"        => $user->id,
                    "attributes"=> [
                        $user
                    ],
                    "links"     => [
                        "self"  => url('/users/'. $user->id)
                    ]
                ]
            ],
            'status' => 'sucess',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'data' => [],
            'status' => 'sucess',
        ], 200);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($user)
    {
        User::onlyTrashed()->findOrFail($user)->restore();
        return response()->json([
            'data' => [],
            'status' => 'sucess',
        ], 200);
    }
}
