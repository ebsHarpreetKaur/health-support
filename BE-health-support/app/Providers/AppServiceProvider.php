<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;

use App\Contracts\DowntimeNotifier;
use App\Contracts\ServerProvider;
use App\Services\DigitalOceanServerProvider;
use App\Services\PingdomDowntimeNotifier;
use App\Services\ServerToolsProvider;

use Illuminate\Support\Facades\View;


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
    public $bindings = [
        ServerProvider::class => DigitalOceanServerProvider::class,
    ];
    public $singletons = [
        DowntimeNotifier::class => PingdomDowntimeNotifier::class,
        ServerProvider::class => ServerToolsProvider::class,
    ];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        View::composer('*', function ($view) {
            $user = session('user'); // Retrieve session data
            $view->with('user', $user); // Share session data with all views
        });
    }
}
