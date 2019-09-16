<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ForgotPasswordRequest
use App\Http\Requests\v1\ResetPasswordRequest
use App\Http\Requests\v1\AlterUserRequest
use App\Mail\RecoverPassword;
use App\Models\v1\User;
use App\Transformers\v1\ListUserTransformer;

class UserController extends Controller
{
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
            $message = 'mailing.email_to_recover_password_was_send';
            $msgAttributes = ['email' => $user->maskEmail];
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
        $password = bcrypt($password);
        $user = User::getUserByResetToken($token);
        if ($user !== null) {
            $message = 'success';
            $httpCode = 200;
            $user->password = $password;
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
    public function index(Request $request)
    {
        $user = User::filter($request->all())->paginateFilter();
        $data = fractal()
            ->collection($user, new ListUserTransformer(), 'users')
            ->paginateWith(new IlluminatePaginatorAdapter($user))
            ->serializeWith(new JsonApiSerializer())
            ->toArray();

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AlterUserRequest $request, User $user)
    {
        $user->fill($request->input())->save();
        $data = fractal()
            ->item($user, new ListUserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $data = fractal()
            ->item($user, new ListUserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
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
        // TODO: Make config file to decide if the user will be returned
        $data = fractal()
            ->item($user, new ListUserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($user)
    {
        $user = User::onlyTrashed()->findOrFail($user);
        $data = fractal()
            ->item($user, new ListUserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        $user->restore();
        return response()->json($data, 200);
    }
}
