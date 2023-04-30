<?php

namespace App\Providers;

use App\Models\Diknas;
use App\Models\User;
use App\Models\School;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $explode = explode(' ', $request->header('Authorization'));
                if ($request->header('User-Type') == 1) {
                    return School::where('api_token', end($explode))->first();
                } else if ($request->header('User-Type') == 2) {
                    return Supervisor::where('api_token', end($explode))->first();
                } else if ($request->header('User-Type') == 3) {
                    return Diknas::where('api_token', end($explode))->first();
                }
            }
        });
    }
}
