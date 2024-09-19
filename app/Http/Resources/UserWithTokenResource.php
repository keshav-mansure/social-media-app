<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWithTokenResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            $this->merge(new UserResource($this)),
            'access_token' => $this->access_token,
        ];
    }
}
