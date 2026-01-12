<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * By default we exclude the XSRF-TOKEN cookie so client-side code can
     * read and send the raw token value in the X-XSRF-TOKEN header. If this
     * cookie is encrypted the header will contain an encrypted value and
     * the CSRF check will fail with a 419.
     *
     * @var array<int, string>
     */
    protected $except = [
        'XSRF-TOKEN',
    ];
}
