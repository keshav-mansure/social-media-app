<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserWithTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function __invoke(RegisterRequest $request)
    {
        $data['password'] = array_merge($request->only('name', 'email'), [
            'password' => Hash::make($request->input('password')),
        ]);
        $user = User::create($data);
        $user->access_token = $user->createToken('access')->accessToken;
        return $this->respondWithMessageAndPayload(new UserWithTokenResource($user), "Register Successful");
    }
}
