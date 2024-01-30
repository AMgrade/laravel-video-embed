<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\Traits;

use Illuminate\Support\Facades\Config;

trait HasIframeConfig
{
    public function getIframeConfig(): array
    {
        $iframeConfig = Config::get('video-embed.iframe');

        $iframeConfig = $iframeConfig[self::KEY] ?? [];

        return $iframeConfig[self::CONFIG_KEY] ?? [];
    }
}
