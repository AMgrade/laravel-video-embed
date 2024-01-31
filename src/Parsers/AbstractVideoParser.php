<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\Traits\HasIframeConfig;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

use function array_keys;
use function array_merge;
use function parse_url;

use const false;
use const null;
use const true;

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
}
