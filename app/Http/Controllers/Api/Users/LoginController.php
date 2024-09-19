<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserWithTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondBadRequest("User not found.");
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->respondBadRequest("Invalid password.");
        }
        $user->access_token = $user->createToken('auth_token')->accessToken;

        return $this->respondWithMessageAndPayload(new UserWithTokenResource($user), "You have successfully logged in.");
    }
}
