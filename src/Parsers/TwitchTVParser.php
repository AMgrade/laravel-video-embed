<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\AbstractVideoParser;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function preg_match;
use function str_contains;
use function trim;

use const false;
use const null;

class TwitchTVParser extends AbstractVideoParser implements VideoParserContract
{
    public const KEY = 'twitch.tv';

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

        $pathTypes = [
            'collections/',
            'video/',
        ];

        foreach ($pathTypes as $type) {
            $result = $this->parseFromPathType($parsed['path'], $type);

            if (null !== $result) {
                return $result;
            }
        }

        return $this->parseFromClip($parsed);
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?(?:clips\.)?twitch\.tv~i', $url);
    }

    /**
     * @see https://dev.twitch.tv/docs/embed/
     * @see https://dev.twitch.tv/docs/embed/video-and-clips/
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $mapTypes = [
            'video' => ['url' => 'https://player.twitch.tv/', 'id' => 'video'],
            'clips' => ['url' => 'https://clips.twitch.tv/embed', 'id' => 'clip'],
            'collections' => ['url' => 'https://player.twitch.tv', 'id' => 'collection'],
        ];

        if (null === ($link = $mapTypes[$type] ?? null)) {
            return null;
        }

        if ($link['id'] ?? false) {
            $urlQuery[$link['id']] = $id;
        }

        return $this->buildIframeCode($link['url'], $urlQuery);
    }

    protected function parseFromPathType(string $parsedPath, string $type): ?array
    {
        if (!str_contains($parsedPath, $type)) {
            return null;
        }

        if (empty($videoId = $this->getVideoId($parsedPath, $type))) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $videoId,
            'type' => trim($type, '/'),
        ];
    }

    protected function parseFromClip(array $parsed): ?array
    {
        if (!str_contains($parsed['host'], 'clip')) {
            return null;
        }

        return [
            'key' => 'twitch.tv',
            'id' => $parsed['path'],
            'type' => 'clips',
        ];
    }
}
