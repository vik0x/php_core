<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\LoginRequest;
use App\Models\v1\User;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function checkForAttempts(Request $request)
    {
        if ($this->limiter()->tooManyAttempts($this->throttleKey($request), config('auth.attempts_amount'), config('auth.lockout_minutes'))) {
            $this->fireLockoutEvent($request);
            return true;
        }
        $this->incrementLoginAttempts($request);
        return false;
    }

    public function login(LoginRequest $request)
    {
        if ($this->checkForAttempts($request)) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));
            return response()->json([
                'errors' => [
                    'message' => trans('auth.throttle', ['seconds' => $seconds]),
                ]
            ], 401);
        }
        extract($request->only('username', 'password'));
        $passes = Auth::guard('web')->attempt(['username' => $username, 'password' => $password]);

        if (!$passes) {
            $passes = Auth::guard('web')->attempt(['email' => $username, 'password' => $password]);
        }

        if ($passes) {
            // Reset failed login attemps
            $this->clearLoginAttempts($request);

            $user = Auth::guard('web')->user();
            $canLogin = $user->can_login;
            $status = $user->status;
            $statusRejected = in_array($status, config('settings.user.status.cant_login'));

            if ($canLogin === 0 || $statusRejected) {
                $message = $statusRejected ? 'auth.account_status.' . $status : 'auth.cant_login';
                return response()->json([
                    'errors' => [
                        'message' => trans($message),
                    ]
                ], 401);
            }

            $token = $user->createToken(config('app.name'));
            return response()->json([
                'response' => [
                    'access_token' => $token->accessToken,
                    'expires_in' => $token->token->expires_at,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        }

        return response()->json([
            'errors' => [
                'message' => trans('auth.failed')
            ]
        ], 401);
    }

    public function logout()
    {
        Auth::user()->token()->revoke();

        return response()->json([
            'response' => [
                    'message' => trans('auth.account_logout'),
                ]
            ], 200);
    }
}
