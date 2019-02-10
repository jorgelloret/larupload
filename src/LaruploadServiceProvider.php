<?php

namespace Mostafaznv\Larupload;

use Illuminate\Database\Schema\Blueprint as BlueprintIlluminate;
use Illuminate\Support\ServiceProvider;
use Mostafaznv\Larupload\Database\Schema\Blueprint;

class LaruploadServiceProvider extends ServiceProvider
{
    // TODO - dpi for resized/cropped images and videos
    // TODO - test s3
    // TODO - upload with url
    // TODO - write some tests
    // TODO - import php-ffmpeg package into the project [NOTICE: wait for a stable version]
    // TODO - add an ability to custom ffmpeg scripts (video and stream)
    // TODO - check possibility of change video ffmpeg script to streaming one (none crop mode).

    const VERSION = '0.0.3';

    public function boot()
    {
        if ($this->app->runningInConsole())
            $this->publishes([__DIR__ . '/../config/config.php' => config_path('larupload.php')], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'larupload');

        $this->registerMacros();
    }

    protected function registerMacros()
    {
        BlueprintIlluminate::macro('upload', function($name, $mode = 'heavy') {
            Blueprint::columns($this, $name, $mode);
        });

        BlueprintIlluminate::macro('dropUpload', function($name, $mode = 'heavy') {
            Blueprint::dropColumns($this, $name, $mode);
        });
    }
}
