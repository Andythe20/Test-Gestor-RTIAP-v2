<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {

        if (Auth::check()) {
            return $this->redirectAfterLogin(Auth::user());
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        return $this->redirectAfterLogin($request->user());
    }

    public function redirectAfterLogin(User $user)
    {
        // admin
        if ($user->role === 'admin') {
            return redirect('/admin');
        }

        // Tenant

        if (! $user->role === 'tenant') {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect('/login')->withErrors(['email' => 'Tu usuario no tiene tenant asignado']);

        }

        return redirect('/t/'.$user->tenant->path);

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');

    }
}
