<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\AbstractVideoParser;
use AMgrade\VideoEmbed\Parsers\Traits\HasFacebookComIframeCode;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function explode;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function trim;

use const null;

class FacebookComParser extends AbstractVideoParser implements VideoParserContract
{
    use HasFacebookComIframeCode;

    public const KEY = 'facebook.com';

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

        $methods = ['Videos', 'Watch'];

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
        return (bool) preg_match('~(?:www\.)?facebook\.com~i', $url);
    }

    protected function parseFromVideos(array $parsed): ?array
    {
        if (!str_contains($parsed['path'], 'videos')) {
            return null;
        }

        if (empty($videoId = $this->getVideoId($parsed['path'], 'videos/'))) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => $videoId,
            'type' => 'single',
        ];
    }

    protected function parseFromWatch(array $parsed): ?array
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
