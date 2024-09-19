<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteUserController extends Controller
{

    public function __invoke()
    {
        $user = Auth::user();
        $user->token()->revoke();
        $user->delete();
        return $this->respondWithMessage("Your account has been deleted successfully.");
    }
}
