<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function store(Request $request, \App\Services\TenantProvisioner $provisioner)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'domain' => ['required', 'string', 'unique:tenants,domain'],
        ]);

        $tenant = \App\Models\Tenant::create([
            'name' => $data['name'],
            'domain' => $data['domain'],
            'status' => 'provisioning',

        ]);

        try {
            $provisioner->provision($tenant);
            return response()->json($tenant->fresh(), 201);
        } catch (\Throwable $e) {
            $tenant->status = 'failed';
            $tenant->save();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
