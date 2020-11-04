<?php

namespace Njed\Toc;

use Njed\Toc\Listeners\GenerateToc;
use Njed\Toc\Tags\Toc;
use Statamic\Events\EntrySaving;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        Toc::class
    ];

    protected $listen = [
        EntrySaving::class => [
            GenerateToc::class
        ]
    ];

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/toc.php' => config_path('toc.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/statamic-toc'),
        ], 'views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'statamic-toc');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/toc.php', 'toc');
    }
}