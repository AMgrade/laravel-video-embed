<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed;

use AMgrade\VideoEmbed\Parsers\FacebookComParser;
use AMgrade\VideoEmbed\Parsers\FbWatchParser;
use AMgrade\VideoEmbed\Parsers\InstagramComParser;
use AMgrade\VideoEmbed\Parsers\TikTokComParser;
use AMgrade\VideoEmbed\Parsers\TwitchTVParser;
use AMgrade\VideoEmbed\Parsers\VideoParser;
use AMgrade\VideoEmbed\Parsers\VimeoComParser;
use AMgrade\VideoEmbed\Parsers\WistiaComParser;
use AMgrade\VideoEmbed\Parsers\YoutubeComParser;
use AMgrade\VideoEmbed\Parsers\YoutuBeParser;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class VideoEmbedServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerParsers();
        $this->registerPublishing();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/video-embed.php',
            'video-embed',
        );
    }

    protected function registerParsers(): void
    {
        $this->app->singleton(VideoParser::class);

        $this->app->singleton(FacebookComParser::class);
        $this->app->singleton(FbWatchParser::class);
        $this->app->singleton(InstagramComParser::class);
        $this->app->singleton(TikTokComParser::class);
        $this->app->singleton(TwitchTVParser::class);
        $this->app->singleton(VimeoComParser::class);
        $this->app->singleton(WistiaComParser::class);
        $this->app->singleton(YoutubeComParser::class);
        $this->app->singleton(YoutuBeParser::class);

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
}
