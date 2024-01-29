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
use function str_starts_with;
use function trim;

use const null;

class InstagramComParser implements VideoParserContract
{
    public const KEY = 'instagram.com';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        if (empty($parsed['path'])) {
            return null;
        }

        $parsed['path'] = trim($parsed['path'], '/');

        $pathTypes = ['reel/', 'reels/', 'p/'];

        return $this->parseFromPaths($pathTypes, $parsed['path']);
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?instagram\.com~i', $url);
    }

    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $url = "https://www.instagram.com/p/{$id}/embed";

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

    protected function parseFromPaths(array $paths, string $parsedPath): ?array
    {
        foreach ($paths as $path) {
            if (!str_starts_with($parsedPath, $path)) {
                continue;
            }

            $videoId = mb_substr(
                $parsedPath,
                mb_strpos($parsedPath, $path) + mb_strlen($path),
            );

            if (empty($videoId)) {
                continue;
            }

            return [
                'key' => self::KEY,
                'id' => $videoId,
                'type' => 'single',
            ];
        }

        return null;
    }
}
