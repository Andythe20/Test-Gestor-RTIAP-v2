<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
            $message = 'Las credenciales proporcionadas son incorrectas.';

            // Log the failed attempt for debugging
            Log::warning('Login failed', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'x_inertia' => (bool) $request->header('X-Inertia'),
                'expects_json' => $request->expectsJson() || $request->wantsJson(),
            ]);

            // Handle Inertia requests separately
            if ($request->header('X-Inertia')) {
                return Inertia::render('Auth/Login', [
                    'errors' => ['email' => $message],
                ])->toResponse($request)->setStatusCode(422);
            }

            // If the client expects JSON, return a plain 422 JSON response (useful for API calls)
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['errors' => ['email' => $message]], 422);
            }

            // Use redirect with session errors so Inertia updates page.props.errors
            return redirect()->back()
                ->withErrors(['email' => $message])
                ->withInput($request->only('email'));
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

        return redirect('/t/' . $user->tenant->path);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
