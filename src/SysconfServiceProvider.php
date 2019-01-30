<?php
namespace Encore\Admin\Sysconf;

use Illuminate\Support\ServiceProvider;

class SysconfServiceProvider extends ServiceProvider{

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes(
            [ __DIR__.'/../resources/assets' => public_path('vendor/laravel-admin-ext/sysconf')],
            'laravel-admin-sysconf'
        );

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-admin-sysconf');

        Sysconf::boot();
    }
}