<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Spatie\Permission\Models\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ForgotPasswordRequest;
use App\Http\Requests\v1\ResetPasswordRequest;
use App\Http\Requests\v1\AlterUserRequest;
use App\Http\Requests\v1\ProfilePictureRequest;
use App\Http\Requests\v1\AssignPermissionsToUserRequest;
use App\Mail\RecoverPassword;
use App\Models\v1\Permission;
use App\Models\v1\User;
use App\Transformers\v1\UserTransformer;

use Illuminate\Support\Facades\Storage;

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
        $message = 'validation.custom.reset_password_invalid_token';
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
            ->collection($user, new UserTransformer(), 'users')
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
        $user->fill($request->input());
        if ($request->isMethod('post')) {
            $role = Role::find($user->role_id);
            $user->password = bcrypt($request->input('password_confirmation'));
        }
        if ($request->isMethod('put')) {
            $user->role_id = $user->getOriginal('role_id');
            $user->password = $user->getOriginal('password');
        }
        $user->save();
        if (isset($role)) {
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
            app('cache')->forget('spatie.permission.cache');
        }
        $data = fractal()
            ->item($user, new UserTransformer(), 'users')
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
            ->item($user, new UserTransformer(), 'users')
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
            ->item($user, new UserTransformer(), 'users')
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
            ->item($user, new UserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        $user->restore();
        return response()->json($data, 200);
    }

    public function uploadPicture(ProfilePictureRequest $request, User $user)
    {
        //Get message to display
        $resp = [
            'message' => trans('validation.custom.invalid_file')
        ];

        if ($request->has('profile_image')) {
            // Get image file
            $image = $request->file('profile_image');
            // Define folder path
            $folder = config('settings.user.profile_picture.path');
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = $folder . '/' . $user->id . '.' . $image->getClientOriginalExtension();
            // Upload image
            $user->uploadOne($image, $folder, env('UPLOAD_ENV', 'public'), $user->id);
            //Get message to display
            $resp = [
                'message' => trans('validation.custom.valid_file'),
                'link' => asset(Storage::url($filePath))
            ];
        }

        return response()->json($resp);
    }

    public function assignPermissionsToUser(AssignPermissionsToUserRequest $request, User $user)
    {
        $permissions = $request->input('permissions');
        $list = [];
        foreach ($permissions as $permissionId) {
            $permission = Permission::find($permissionId);
            if (!in_array($permission->parent_id, $permissions) && !in_array($permission->parent_id, array_keys($list))) {
                $list[$permission->parent_id] = Permission::find($permission->parent_id);
            }
            $list[$permissionId] = $permission;
        }
        $user->syncPermissions($list);
        app('cache')->forget('spatie.permission.cache');
        $data = fractal()
            ->item($user, new UserTransformer(), 'users')
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
    }

    public function fetchUser(Request $request)
    {
        $user = $request->user();
        $data = fractal()
            ->item($user, new UserTransformer(), 'users')
            ->includePermissions()
            ->serializeWith(new JsonApiSerializer())
            ->toArray();
        return response()->json($data, 200);
    }
}
