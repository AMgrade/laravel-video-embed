<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\Traits\HasYoutubeComIframeCode;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function preg_match;
use function trim;

use const null;

class YoutuBeParser implements VideoParserContract
{
    use HasYoutubeComIframeCode;

    public const KEY = 'youtu.be';

    public const CONFIG_KEY = 'attributes';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        $parsed['path'] = trim($parsed['path'], '/');

        if (empty($parsed['path'])) {
            return null;
        }

        return [
            'key' => self::KEY,
            'id' => trim($parsed['path'], '/'),
            'type' => 'single',
        ];
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?youtu\.be~i', $url);
    }
}
