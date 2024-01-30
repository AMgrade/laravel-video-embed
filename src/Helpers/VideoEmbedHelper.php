<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Helpers;

use AMgrade\VideoEmbed\Parsers\VideoParser;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

use function array_keys;
use function array_merge;
use function in_array;
use function mb_strlen;
use function mb_strtolower;
use function mb_substr;
use function parse_url;
use function substr_replace;

use const null;

class VideoEmbedHelper
{
    protected static array $iframeConfig = [];

    protected static array $parsersMapping = [];

    public static function getVideoUrlAttributes($value): array
    {
        return null !== $value
            ? self::getFormattedVideoUrl(
                $value,
                array_keys(Config::get('video-embed.video-parsers', [])),
            )
            : [];
    }

    public static function getVideoIframeCode(?array $videoUrl): ?string
    {
        if (null === $videoUrl) {
            return null;
        }

        $key = $videoUrl['key'] ?? null;

        $iframeDataByKey = self::getIframeDataByKey($key);

        $urlQuery = $iframeDataByKey['query'] ?? [];
        $attributes = $iframeDataByKey['attributes'] ?? [];

        return Container::getInstance()->make(VideoParser::class)->getIframeCode(
            $key,
            $videoUrl['id'] ?? '',
            $videoUrl['original'] ?? '',
            $urlQuery,
            $attributes,
            $videoUrl['type'] ?? null,
        );
    }

    protected static function getFormattedVideoUrl(string $videoUrl, array $keys = []): array
    {
        $parseUrl = parse_url($videoUrl);

        $urlDomainLength = mb_strlen($parseUrl['scheme']) + mb_strlen($parseUrl['host']);

        $startPosition = 0;

        $url = substr_replace(
            $videoUrl,
            mb_strtolower(mb_substr($videoUrl, $startPosition, $urlDomainLength)),
            $startPosition,
            $urlDomainLength,
        );

        return Container::getInstance()
            ->make(VideoParser::class)
            ->parse($url, $keys) ?? [];
    }

    protected static function getIframeDataByKey(?string $key = null): array
    {
        self::$parsersMapping = Config::get('video-embed.video-parsers', []);

        if (null === $key || !isset(self::$parsersMapping[$key])) {
            throw new InvalidArgumentException(
                "Provided parser key {$key} is invalid",
            );
        }

        $defaultIframeConfig = self::getIframeConfig('default');

        $iframeConfig = Container::getInstance()
            ->make(self::$parsersMapping[$key])
            ->getIframeConfig() ?? [];

        if (!in_array('attributes', $iframeConfig, true)) {
            $iframeConfig['attributes'] = [
                'height' => $defaultIframeConfig['attributes']['height'],
                'width' => $defaultIframeConfig['attributes']['width'],
            ];
        }

        $iframeConfig['attributes'] = array_merge(
            $iframeConfig['attributes'],
            [
                'frameborder' => '0',
                'allow' => 'autoplay; fullscreen; clipboard-write; encrypted-media; picture-in-picture',
                'allowfullscreen' => 'true',
                'allowtransparency' => 'true',
            ],
        );

        return $iframeConfig;
    }

    protected static function getIframeConfig(?string $key = null): array
    {
        if (empty(self::$iframeConfig)) {
            self::$iframeConfig = Config::get('video-embed.iframe');
        }

        return $key ? (self::$iframeConfig[$key] ?? []) : self::$iframeConfig;
    }
}
