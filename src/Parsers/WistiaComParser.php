<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\Traits\HasIframeConfig;

use function http_build_query;
use function implode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function preg_match;
use function sprintf;

use const null;

class WistiaComParser implements VideoParserContract
{
    use HasIframeConfig;

    public const KEY = 'wistia.com';

    public const CONFIG_KEY = 'attributes';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        $videoId = mb_substr(
            $parsed['path'],
            mb_strpos($parsed['path'], 'medias/') + mb_strlen('medias/'),
        );

        if (empty($videoId)) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $videoId,
            'type' => 'single',
        ];
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?wistia\.com~i', $url);
    }

    /**
     * @see https://wistia.com/support/developers/embed-links
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $url = "https://fast.wistia.net/embed/iframe/{$id}";

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
