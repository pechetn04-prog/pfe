<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'Adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['nullable', 'boolean'],
        ];
    }
}
