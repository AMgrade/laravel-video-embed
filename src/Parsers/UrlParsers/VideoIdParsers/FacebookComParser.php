<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers;

use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParserContract;
use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers\Traits\HasFacebookComIframeCode;

use function explode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function trim;

use const null;

class FacebookComParser implements VideoIdParserContract
{
    use HasFacebookComIframeCode;

    public const KEY = 'facebook.com';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        if (empty($parsed['path'])) {
            return null;
        }

        $parsed['path'] = trim($parsed['path'], '/');

        $methods = ['Videos', 'Watch'];

        foreach ($methods as $method) {
            $method = "getFrom{$method}";

            if (null !== ($result = $this->{$method}($parsed))) {
                return $result;
            }
        }

        return null;
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?facebook\.com~i', $url);
    }

    protected function getFromVideos(array $parsed): ?array
    {
        if (!str_contains($parsed['path'], 'videos')) {
            return null;
        }

        $videoId = mb_substr(
            $parsed['path'],
            mb_strpos($parsed['path'], 'videos/') + mb_strlen('videos/'),
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

    protected function getFromWatch(array $parsed): ?array
    {
        if (!str_starts_with($parsed['path'], 'watch')) {
            return null;
        }

        $query = explode('=', $parsed['query'] ?? '');

        if ('v' === $query[0] && !empty($query[1])) {
            return [
                'key' => self::KEY,
                'id' => $query[1],
                'type' => 'single',
            ];
        }

        return null;
    }
}
