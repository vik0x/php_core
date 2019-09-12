<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\v1\User;
use App\Http\Requests\v1\UserEditRequest;
use App\Http\Requests\v1\UserCreateRequest;

class UserController extends Controller
{
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
