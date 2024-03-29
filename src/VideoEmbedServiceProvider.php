<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed;

use AMgrade\VideoEmbed\Parsers\VideoParser;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class VideoEmbedServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerPublishing();
        $this->registerVideoParser();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/video-embed.php',
            'video-embed',
        );
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\PublishCommand::class,
            ]);
        }
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/video-embed.php' => $this->app->configPath('video-embed.php'),
            ], 'video-embed-config');
        }
    }

    protected function registerVideoParser(): void
    {
        $this->app->singleton(VideoParser::class);
    }
}
