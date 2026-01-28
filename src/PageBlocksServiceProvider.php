<?php

namespace DejoDev\PageBlocks;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PageBlocksServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!app()->environment('testing')) {
            $this->app->singleton(PageBlocksManager::class);
        }
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/page-blocks.php' => config_path('page-blocks.php'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/page-blocks'),
        ], 'page-blocks');

        $this->mergeConfigFrom(__DIR__ . '/../config/page-blocks.php', 'page-blocks');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'page-blocks');

        Blade::component('page-blocks::components.blocks', 'page-blocks::blocks');
    }
}
