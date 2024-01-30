<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Helpers;

use AMgrade\VideoEmbed\Parsers\FacebookComParser;
use AMgrade\VideoEmbed\Parsers\FbWatchParser;
use AMgrade\VideoEmbed\Parsers\InstagramComParser;
use AMgrade\VideoEmbed\Parsers\TikTokComParser;
use AMgrade\VideoEmbed\Parsers\TwitchTVParser;
use AMgrade\VideoEmbed\Parsers\VideoParser;
use AMgrade\VideoEmbed\Parsers\VimeoComParser;
use AMgrade\VideoEmbed\Parsers\WistiaComParser;
use AMgrade\VideoEmbed\Parsers\YoutubeComParser;
use AMgrade\VideoEmbed\Parsers\YoutuBeParser;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

use const null;

class VideoParserHelper
{
    protected static array $iframeConfig = [];

    protected static array $parsersMapping = [];

    protected function __construct(
        FacebookComParser $facebookComParser,
        FbWatchParser $fbWatchParser,
        InstagramComParser $instagramComParser,
        TikTokComParser $tikTokComParser,
        TwitchTVParser $twitchTVParser,
        VimeoComParser $vimeoComParser,
        WistiaComParser $wistiaComParser,
        YoutubeComParser $youtubeComParser,
        YoutuBeParser $youtuBeParser,
    ) {
        self::$parsersMapping = [
            $facebookComParser::KEY => $facebookComParser,
            $fbWatchParser::KEY => $fbWatchParser,
            $instagramComParser::KEY => $instagramComParser,
            $tikTokComParser::KEY => $tikTokComParser,
            $twitchTVParser::KEY => $twitchTVParser,
            $vimeoComParser::KEY => $vimeoComParser,
            $wistiaComParser::KEY => $wistiaComParser,
            $youtubeComParser::KEY => $youtubeComParser,
            $youtuBeParser::KEY => $youtuBeParser,
        ];
    }

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

        return Container::getInstance()->make(VideoParser::class)->getIframeCode(
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
            ->make(VideoParser::class)
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
        if (!isset(self::$parsersMapping[$key])) {
            throw new InvalidArgumentException(
                "Provided parser key {$key} is invalid",
            );
        }

        $defaultIframeConfig = self::getIframeConfig('default');

        $iframeConfig = self::$parsersMapping[$key]->getIframeConfig() ?? [];

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
            ]
        );

        return $iframeConfig;
    }
}
