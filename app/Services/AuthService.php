<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new user and log them in.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return $user;
    }

    /**
     * Attempt to authenticate a user.
     */
    public function login(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    /**
     * Log the user out and invalidate session.
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
