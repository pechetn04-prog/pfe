<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password'   => ['nullable', 'string', 'min:8'],
            'role'       => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone'  => ['nullable', 'string', 'max:50'],
            'specialite' => ['nullable', 'string', 'max:255'],
            'specialites'=> ['nullable', 'array'],
            'actif'      => ['nullable', 'boolean'],
        ];
    }
}