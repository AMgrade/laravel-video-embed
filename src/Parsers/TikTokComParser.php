<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use function http_build_query;
use function implode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function preg_match;
use function sprintf;
use function str_contains;
use function trim;

use const null;

class TikTokComParser implements VideoParserContract
{
    public const KEY = 'tiktok.com';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        if (empty($parsed['path'])) {
            return null;
        }

        $parsed['path'] = trim($parsed['path'], '/');

        $methods = ['Video', 'Vm'];

        foreach ($methods as $method) {
            $method = "parseFrom{$method}";

            if (null !== ($result = $this->{$method}($parsed))) {
                return $result;
            }
        }

        return null;
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?(?:vm\.)?tiktok\.com~i', $url);
    }

    /**
     * @see https://developers.tiktok.com/doc/embed-videos/
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $url = 'https://www.tiktok.com/embed/v2/';

        $url = "{$url}{$id}";

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

    protected function parseFromVideo(array $parsed): ?array
    {
        if (!str_contains($parsed['path'], 'video')) {
            return null;
        }

        $videoId = mb_substr(
            $parsed['path'],
            mb_strpos($parsed['path'], 'video/') + mb_strlen('video/'),
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

    protected function parseFromVm(array $parsed): ?array
    {
        if (!str_contains($parsed['host'], 'vm')) {
            return null;
        }

        if (empty($parsed['path'])) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $parsed['path'],
            'type' => 'single',
        ];
    }
}
