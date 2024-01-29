<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\Traits;

use Illuminate\Support\Facades\Config;

trait HasIframeConfig
{
    public function getIframeConfig(): array
    {
        $iframeConfig = Config::get('video-embed.iframe');

        return $iframeConfig[self::KEY][self::CONFIG_KEY ?? 'attributes'];
    }
}
