<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Show the registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        return redirect()->route('chat');
    }

    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->authService->login($request->validated())) {
            $request->session()->regenerate();

            return redirect()->route('chat');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login');
    }
}
