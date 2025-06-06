<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... bagian $middleware dan $middlewareGroups seperti semula ...

    /**
     * Alias‐alias middleware yang bisa dipakai di route.
     * Laravel akan menggabungkan $routeMiddleware ke dalam $middlewareAliases di runtime,
     * tapi kita perlu menambahkan 'isAdmin' ke sini agar getRouteMiddleware() mengenalinya.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth'            => \App\Http\Middleware\Authenticate::class,
        'auth.basic'      => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'    => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'   => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'             => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'           => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'=> \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'          => \App\Http\Middleware\ValidateSignature::class,
        'throttle'        => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'        => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin'         => \App\Http\Middleware\IsAdmin::class,
    ];

    /**
     * Pada Laravel 12+, ada juga properti $middlewareAliases.
     * Kita akan gabungkan keduanya agar konsisten, tapi
     * yang utama untuk route adalah $routeMiddleware di atas.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin' => \App\Http\Middleware\IsAdmin::class,
    ];
}
