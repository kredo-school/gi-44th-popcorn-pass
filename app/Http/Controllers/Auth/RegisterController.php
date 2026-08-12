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

            // Birthday
            'date_of_birth' => ['nullable', 'date', 'before:today'],

            'gender' => ['nullable', 'string', 'max:30'],
            'occupation' => [
                'nullable',
                'in:student,company_employee,government_employee,self_employed,freelancer,part_time,homemaker,unemployed,other'
            ],
        ], [
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'date_of_birth.before' => 'Birthday must be before today.',
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
            'password_hash' => $data['password'],

            // Birthdayをそのまま保存
            'date_of_birth' => $data['date_of_birth'] ?? null,

            'gender' => $data['gender'] ?? null,
            'occupation' => $data['occupation'] ?? null,
            'points' => 0,
            'role' => 1,
            'is_active' => 1,
        ]);
    }
}
