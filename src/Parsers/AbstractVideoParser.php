<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\Traits\HasIframeConfig;

use function http_build_query;
use function implode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function sprintf;

class AbstractVideoParser
{
    use HasIframeConfig;

    protected function buildIframeCode(string $url, array $urlQuery = []): string
    {
        if (!empty($urlQuery)) {
            $url .= '?'.http_build_query($urlQuery);
        }

        $string = '<iframe %s />';

        $attributes['src'] = $url;

        $keyedAttributes = [];

        foreach ($attributes as $key => $value) {
            $keyedAttributes[] = "{$key}=\"{$value}\"";
        }

        return sprintf($string, implode(' ', $keyedAttributes));
    }

    protected function getVideoId(string $parsedPath, string $path): string
    {
        return mb_substr(
            $parsedPath,
            mb_strpos($parsedPath, $path) + mb_strlen($path),
        );
    }
}
