<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\AbstractVideoParser;
use AMgrade\VideoEmbed\Parsers\Traits\HasYoutubeComIframeCode;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function explode;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function trim;

use const null;

class YoutubeComParser extends AbstractVideoParser implements VideoParserContract
{
    use HasYoutubeComIframeCode;

    public const KEY = 'youtube.com';

    public const CONFIG_KEY = 'attributes';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        if (empty($parsed['path'])) {
            return null;
        }

        $parsed['path'] = trim($parsed['path'], '/');

        $methods = ['Playlist', 'Shorts', 'Watch'];

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
        return (bool) preg_match('~(?:www\.)?(?:m\.)?youtube\.com~i', $url);
    }

    protected function parseFromPlaylist(array $parsed): ?array
    {
        if (
            null === ($parsed['query'] ?? null) ||
            !str_contains($parsed['query'] ?? '', 'list')
        ) {
            return null;
        }

        if (empty($playlistId = $this->getVideoId($parsed['query'], 'list='))) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $playlistId,
            'type' => 'playlist',
        ];
    }

    protected function parseFromShorts(array $parsed): ?array
    {
        if (!str_starts_with($parsed['path'], 'shorts')) {
            return null;
        }

        if (empty($videoId = $this->getVideoId($parsed['path'], 'shorts/'))) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $videoId,
            'type' => 'shorts',
        ];
    }

    protected function parseFromWatch(array $parsed): ?array
    {
        if (!str_starts_with($parsed['path'], 'watch')) {
            return null;
        }

        $query = explode('=', $parsed['query'] ?? '');

        if ('v' === $query[0]) {
            return [
                'key' => self::KEY,
                'id' => $query[1],
                'type' => 'single',
            ];
        }

        return null;
    }
}
