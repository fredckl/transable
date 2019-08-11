<?php


namespace Fredckl\Transable;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot ()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->_publishFiles();
    }

    public function register ()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/transable.php', 'transable');
    }

    protected function _publishFiles ()
    {
        $this->publishes([
            __DIR__ . '/../config/transable.php' => \config_path('transable.php'),
        ], 'transable');
    }
}
