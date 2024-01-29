<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\Traits;

use function http_build_query;
use function implode;
use function sprintf;

use const false;
use const null;

trait HasYoutubeComIframeCode
{
    /**
     * @see https://developers.google.com/youtube/iframe_api_reference
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $mapTypes = [
            'playlist' => [
                'url' => 'https://www.youtube.com/embed/videoseries',
                'id' => 'list',
            ],
            'single' => ['url' => "https://www.youtube.com/embed/{$id}"],
        ];

        if (null === ($url = $mapTypes[$type] ?? null)) {
            return null;
        }

        if ($url['id'] ?? false) {
            $urlQuery[$url['id']] = $id;
        }

        $url = $url['url'].'?'.http_build_query($urlQuery);

        $string = '<iframe %s />';

        $attributes['src'] = $url;

        $keyedAttributes = [];

        foreach ($attributes as $key => $value) {
            $keyedAttributes[] = "{$key}=\"{$value}\"";
        }

        return sprintf($string, implode(' ', $keyedAttributes));
    }
}
