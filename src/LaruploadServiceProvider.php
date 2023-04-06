<?php

namespace Mostafaznv\Larupload;

use Illuminate\Database\Schema\Blueprint as BlueprintIlluminate;
use Illuminate\Support\ServiceProvider;
use Mostafaznv\Larupload\Database\Schema\Blueprint;
use Mostafaznv\Larupload\Enums\LaruploadMode;

class LaruploadServiceProvider extends ServiceProvider
{
    // TODO - convert functions/traits/concerns to action
    // TODO - add index to database columns
    // TODO - add some comments to help IDEs to show attachment functions
    // TODO - remove meta data from file
    // TODO - 100% test coverage

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'larupload');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('larupload.php')], 'config');
            $this->publishes([__DIR__ . '/../migrations/' => database_path('migrations')], 'migrations');
            $this->publishes([__DIR__ . '/../translations/' => resource_path('lang/vendor/larupload')]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'larupload');

        $this->registerMacros();
    }

    protected function registerMacros()
    {
        BlueprintIlluminate::macro('upload', function(string $name, LaruploadMode $mode = LaruploadMode::HEAVY) {
            Blueprint::columns($this, $name, $mode);
        });

        BlueprintIlluminate::macro('dropUpload', function(string $name) {
            Blueprint::dropColumns($this, $name);
        });
    }
}
