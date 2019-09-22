<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\LoginRequest;
use App\Models\v1\User;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    protected $cookie;

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

        $clientId = $secret = '';
        extract($request->allValid());
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

            return $this->oauthToken('password', $user, $clientId, $secret, $password);
        }

        return response()->json([
            'errors' => [
                'message' => trans('auth.failed')
            ]
        ], 401);
    }

    public function oauthTtoken($grantType, $user, $clientId, $secret, $password)
    {

        $app = app();
        $this->cookie = $app->make('cookie');

        $params = array_merge([
            'username'=>$user->email,
            'password'=>$password,
        ], [
            'client_id'=>$clientId,
            'client_secret'=>$$secret,
            'grant_type'=>$grantType
        ]);

        try {
            $http = new Client;

            $response = $http->post(env('APP_URL') . '/oauth/token', [
                'form_params' => $params
            ]);
        } catch (RequestException $e) {
            return response()->json([
                'errors' => [
                    'message' => trans('auth.failed')
                ]
            ], 401);
        }

        $result = json_decode((string) $response->getBody());

        return response()->json([
            'response' => [
                'access_token' => $result->access_token,
                'expires_in' => $result->expires_in,
                'token_type' => 'Bearer',
            ]
        ], 200);
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
