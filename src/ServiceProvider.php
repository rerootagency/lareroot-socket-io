<?php
namespace RerootAgency\LaReRootSocketIO;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    const SIGNATURE = 'lareroot-socket-io';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path(self::SIGNATURE.'.php'),
        ], self::SIGNATURE.'-config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('lareroot-publisher', 'RerootAgency\LaReRootSocketIO\Services\Publisher');

        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', self::SIGNATURE
        );
    }
}
