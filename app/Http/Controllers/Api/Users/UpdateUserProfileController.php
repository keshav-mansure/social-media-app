<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserProfileController extends Controller
{

    public function __invoke(UpdateUserProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->only(['name']);
        $user->update($data);
        return $this->respondWithMessageAndPayload(new UserResource($user), 'Your profile has been updated successfully');
    }
}
