<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => ['required', 'email', Rule::exists('users', 'email')->whereNull('deleted_at')],
            'password' => ['required', 'min:6', 'max:255'],
        ];
    }
}
