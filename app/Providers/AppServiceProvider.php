<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        // Trust the ngrok proxy
        if (request()->server->has('HTTP_X_FORWARDED_PROTO') && 
            request()->server->get('HTTP_X_FORWARDED_PROTO') == 'https') {
            request()->server->set('HTTPS', true);
        }
    }
}
