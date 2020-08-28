<?php

namespace App\Providers;

use App\User;
use App\Api_auth;
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
            if ($request->header('api-token') && $request->header('admin-id') || $request->header('superadmin-id') || $request->header('event-org-id') || $request->header('order-mngt-id')) {
                if(!empty($request->header('admin-id'))){
                  $user_id = $request->header('admin-id');
                  $user_type = "A";
                } elseif (!empty($request->header('superadmin-id'))) {
                  $user_id = $request->header('superadmin-id');
                  $user_type = "SA";
                } elseif (!empty($request->header('event-org-id'))) {
                  $user_id = $request->header('event-org-id');
                  $user_type = "EO";
                } elseif (!empty($request->header('order-mngt-id'))) {
                    $user_id = $request->header('order-mngt-id');
                    $user_type = "OM";
                  }

                $where_array = array(
                  "token" =>$request->header('api-token'),
                  "user_id" =>$user_id,
                  "user_type" =>$user_type,
                );
               $user = Api_auth::where($where_array)->first();
                return $user;
            }
        });
    }
}
