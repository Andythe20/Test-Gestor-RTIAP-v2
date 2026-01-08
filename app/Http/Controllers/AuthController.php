<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->tenant_id) {
                return redirect('/tenant/dashboard');
            } else {
                return redirect('/admin/tenants');
            }
        }
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        Auth::login($user);

        if ($user->tenant_id) {
            // It's a tenant user, make tenant current
            $tenant = $user->tenant;
            $tenant->makeCurrent();
            return redirect('/tenant/dashboard');
        } else {
            // Admin user
            return redirect('/admin/tenants');
        }

        return back()->withErrors(['email' => 'Unable to determine user role.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
