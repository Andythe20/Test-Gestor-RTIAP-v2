<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:256'],
            'is_admin' => ['required'],
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
        ]);

        $isAdmin = $request->boolean('is_admin');

        if (empty($data['tenant_id']) && ! $isAdmin) {
            return response()->json(['message' => 'Es necesario el nombre del tenant para un no-admin'], 422);
        }

        $tenant = null;

        if (! $isAdmin) {
            $tenant = Tenant::on('landlord')
                ->findOrFail($data['tenant_id']);
        }

        $user = User::on('landlord')->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $isAdmin,
            'tenant_id' => $isAdmin ? null : $tenant->id,
            'database' => $isAdmin ? null : $tenant->database,
        ]);

        return response()->json($user, 201);
    }
}
