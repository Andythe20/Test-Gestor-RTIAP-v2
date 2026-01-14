<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Determine if the incoming request is an Inertia client visit.
     *
     * We centralize the check so controllers call `$this->isInertiaRequest($request)`.
     */
    protected function isInertiaRequest(Request $request): bool
    {
        return (bool) $request->header('X-Inertia');
    }
}
