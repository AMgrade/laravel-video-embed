<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\AbstractVideoParser;
use AMgrade\VideoEmbed\Parsers\VideoParserContract;

use function preg_match;
use function str_contains;
use function trim;

use const null;

class TikTokComParser extends AbstractVideoParser implements VideoParserContract
{
    public const KEY = 'tiktok.com';

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
        return $this->buildIframeCode(
            "https://www.tiktok.com/embed/v2/{$id}",
            $urlQuery,
        );
    }

    protected function parseFromVideo(array $parsed): ?array
    {
        if (!str_contains($parsed['path'], 'video')) {
            return null;
        }

        if (empty($videoId = $this->getVideoId($parsed['path'], 'video/'))) {
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
