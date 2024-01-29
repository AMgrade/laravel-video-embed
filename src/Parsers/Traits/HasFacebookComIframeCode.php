<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\Traits;

use Illuminate\Support\Facades\Config;
use function http_build_query;
use function implode;
use function sprintf;

use const null;

trait HasFacebookComIframeCode
{
    /**
     * @see https://developers.facebook.com/docs/plugins/embedded-video-player/
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $url = 'https://www.facebook.com/plugins/video.php';

        $urlQuery['href'] = $original;

        $url .= '?'.http_build_query($urlQuery);

        $string = '<iframe %s />';

        $attributes['src'] = $url;

        $keyedAttributes = [];

        foreach ($attributes as $key => $value) {
            $keyedAttributes[] = "{$key}=\"{$value}\"";
        }

        return sprintf($string, implode(' ', $keyedAttributes));
    }
}
