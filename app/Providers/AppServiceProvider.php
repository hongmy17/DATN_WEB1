<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authenticate::redirectUsing(fn () => route('login'));
        AuthenticationException::redirectUsing(fn () => route('login'));

        RedirectIfAuthenticated::redirectUsing(function () {
            $user = auth()->user();

            if ($user && (int) $user->role === 1) {
                return '/admin';
            }

            return route('home');
        });
    }
}
