<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\AbstractVideoParser;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function preg_match;
use function str_starts_with;
use function trim;

use const null;

class InstagramComParser extends AbstractVideoParser implements VideoParserContract
{
    public const KEY = 'instagram.com';

    public const CONFIG_KEY = 'query';

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
        return $this->buildIframeCode(
            "https://www.instagram.com/p/{$id}/embed",
            $urlQuery,
        );
    }

    protected function parseFromPaths(array $paths, string $parsedPath): ?array
    {
        foreach ($paths as $path) {
            if (!str_starts_with($parsedPath, $path)) {
                continue;
            }

            if (empty($videoId = $this->getVideoId($parsedPath, $path))) {
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
