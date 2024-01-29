<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Helpers;

use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParser;
use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers\FacebookComParser;
use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers\InstagramComParser;
use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers\TikTokComParser;
use AMgrade\VideoEmbed\Parsers\UrlParsers\VideoIdParsers\TwitchTVParser;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

use function explode;
use function method_exists;
use function ucfirst;

use const null;

class VideoIdParserHelper
{
    protected static array $iframeConfig = [];

    public static function getVideoUrlAttributes($value): array
    {
        return null !== $value
            ? self::getFormattedVideoUrl(
                $value,
                Config::get('video-embed.video-parsers.keys', []),
            )
            : [];
    }

    public static function getVideoIframeCode(?array $videoUrl): ?string
    {
        if (null === $videoUrl) {
            return null;
        }

        $key = $videoUrl['key'] ?? null;

        [$urlQuery, $attributes] = self::getIframeDataByKey($key);

        return Container::getInstance()->make(VideoIdParser::class)->getIframeCode(
            $key,
            $videoUrl['id'] ?? '',
            $videoUrl['original'] ?? '',
            $urlQuery,
            $attributes,
            $videoUrl['type'] ?? null,
        );
    }

    protected static function getFormattedVideoUrl(string $videoUrl, array $keys = []): ?array
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
            ->make(VideoIdParser::class)
            ->parse($url, $keys);
    }

    protected static function getIframeConfig(?string $key = null): array
    {
        if (empty(self::$iframeConfig)) {
            self::$iframeConfig = Config::get('video-embed.iframe');
        }

        return $key ? (self::$iframeConfig[$key] ?? []) : self::$iframeConfig;
    }

    protected static function getIframeDataByKey(string $key): array
    {
        $keyPaths = explode('.', $key);

        $key = '';

        foreach ($keyPaths as $keyPath) {
            $key .= ucfirst(Str::camel($keyPath));
        }

        $method = "get{$key}Data";

        return method_exists(static::class, $method)
            ? self::{$method}()
            : [[], self::getKeyedAttributes()];
    }

    protected static function getFacebookComData(): array
    {
        $iframeConfig = self::getIframeConfig(FacebookComParser::KEY);

        return [
            $iframeConfig['query'],
            self::getKeyedAttributes(),
        ];
    }

    protected static function getFbWatchData(): array
    {
        return self::getFacebookComData();
    }

    protected static function getInstagramComData(): array
    {
        $iframeConfig = self::getIframeConfig(InstagramComParser::KEY);

        return [
            [],
            self::getKeyedAttributes(
                $iframeConfig['attributes']['height'],
                $iframeConfig['attributes']['width'],
            ),
        ];
    }

    protected static function getTikTokComData(): array
    {
        $iframeConfig = self::getIframeConfig(TikTokComParser::KEY);

        return [
            [],
            self::getKeyedAttributes(
                $iframeConfig['attributes']['height'],
                $iframeConfig['attributes']['width'],
            ),
        ];
    }

    protected static function getTwitchTvData(): array
    {
        $iframeConfig = self::getIframeConfig(TwitchTVParser::KEY);

        return [
            $iframeConfig['query'],
            self::getKeyedAttributes(),
        ];
    }

    protected static function getKeyedAttributes(?int $height = null, ?int $width = null): array
    {
        $iframeConfig = self::getIframeConfig('default');

        return [
            'height' => $height ?? $iframeConfig['attributes']['height'],
            'width' => $width ?? $iframeConfig['attributes']['width'],
            'frameborder' => '0',
            'allow' => 'autoplay; fullscreen; clipboard-write; encrypted-media; picture-in-picture',
            'allowfullscreen' => 'true',
            'allowtransparency' => 'true',
        ];
    }
}
