<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function redirectTo(): string
    {
        return route('mypage.dashboard');
    }

    protected function validator(array $data): ValidatorContract
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'gender' => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:100'],
        ], [
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
        ]);
    }

    protected function create(array $data): User
    {
        return User::create([
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => $data['password'], // 'hashed' castで自動ハッシュ化される
            'date_of_birth' => isset($data['age'])
                ? now()->subYears((int) $data['age'])->startOfYear()->toDateString()
                : null,
            'gender' => $data['gender'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'points' => 0,
            'role' => 1, // customer — 固定値
            'is_active' => 1,
        ]);
    }
}