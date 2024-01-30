<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Helpers;

use AMgrade\VideoEmbed\Parsers\VideoParser;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

use function array_keys;
use function array_merge;
use function mb_strlen;
use function mb_strtolower;
use function mb_substr;
use function parse_url;
use function substr_replace;

use const null;

class VideoEmbedHelper
{
    protected static ?array $iframeConfig = null;

    protected static ?array $parsersMapping = null;

    public static function getVideoUrlAttributes($value): array
    {
        return null !== $value
            ? self::getFormattedVideoUrl(
                $value,
                array_keys(self::getVideoParsersConfig()),
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
        $parsedUrl = parse_url($videoUrl);

        $urlDomainLength = mb_strlen($parsedUrl['scheme']) + mb_strlen($parsedUrl['host']);

        $url = substr_replace(
            $videoUrl,
            mb_strtolower(mb_substr($videoUrl, 0, $urlDomainLength)),
            0,
            $urlDomainLength,
        );

        return Container::getInstance()
            ->make(VideoParser::class)
            ->parse($url, $keys) ?? [];
    }

    protected static function getIframeDataByKey(?string $key = null): array
    {
        if (null === $key || !isset(self::getVideoParsersConfig()[$key])) {
            throw new InvalidArgumentException(
                "Provided parser key '{$key}' is invalid",
            );
        }

        $defaultIframeConfig = self::getIframeConfig('default');

        $iframeConfig = Container::getInstance()
            ->make(self::$parsersMapping[$key])
            ->getIframeConfig() ?? [];

        if (!isset($iframeConfig['attributes'])) {
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
        if (null === self::$iframeConfig) {
            self::$iframeConfig = Config::get('video-embed.iframe', []);
        }

        return $key ? (self::$iframeConfig[$key] ?? []) : self::$iframeConfig;
    }

    protected static function getVideoParsersConfig(): array
    {
        if (null === self::$parsersMapping) {
            self::$parsersMapping = Config::get('video-embed.video-parsers', []);
        }

        return self::$parsersMapping;
    }
}
