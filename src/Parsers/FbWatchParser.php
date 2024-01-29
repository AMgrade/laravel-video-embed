<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use AMgrade\VideoEmbed\Parsers\Traits\HasFacebookComIframeCode;
use Illuminate\Support\Facades\Log;
use McMatters\Ticl\Client;
use Throwable;

use function explode;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function parse_url;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function trim;
use function urldecode;
use function urlencode;

use const null;
use const PHP_URL_PATH;
use const PHP_URL_QUERY;

class FbWatchParser implements VideoParserContract
{
    use HasFacebookComIframeCode;

    public const KEY = 'fb.watch';

    public function parse(array $parsed, string $url): ?array
    {
        if (!$this->validate($parsed['host'])) {
            return null;
        }

        try {
            $followingUrl = (new Client())
                ->withHeaders(['User-Agent' => 'Ticl'])
                ->get($url)
                ->getInfoByKey('url');

            if (null === $followingUrl) {
                return null;
            }

            if (null === $videoId = $this->parseFromQueryNext($followingUrl)) {
                $videoId = $this->parseFromVideosPath($followingUrl);
            }

            if (empty($videoId)) {
                return null;
            }

            return [
                'key' => self::KEY,
                'id' => $videoId,
                'type' => 'single',
            ];
        } catch (Throwable $e) {
            Log::error($e->getMessage(), [
                'class' => __CLASS__,
                'method' => __FUNCTION__,
                'url' => $url,
            ]);

            return null;
        }
    }

    public function validate(string $url): bool
    {
        return (bool) preg_match('~(?:www\.)?(?:fb\.(?:watch|com)|fbwat\.ch)~i', $url);
    }

    protected function parseFromQueryNext(string $followingUrl): ?string
    {
        if (!str_starts_with($followingUrl, 'https://www.facebook.com/login/')) {
            return null;
        }

        if (!str_contains($followingUrl, 'next='.urlencode('https://www.facebook.com/watch/'))) {
            return null;
        }

        if (null === ($url = $this->parseQueryUrl($followingUrl, 'next'))) {
            return null;
        }

        $url = urldecode($url);

        return $this->parseQueryUrl($url, 'v');
    }

    protected function parseFromVideosPath(string $followingUrl): string
    {
        $parsed['path'] = parse_url($followingUrl, PHP_URL_PATH);
        $parsed['path'] = trim($parsed['path'], '/');

        $videoId = mb_substr(
            $parsed['path'],
            mb_strpos($parsed['path'], 'videos/') + mb_strlen('videos/'),
        );

        return trim($videoId);
    }

    protected function parseQueryUrl(string $url, string $searchKey): ?string
    {
        $query = explode('&', parse_url($url, PHP_URL_QUERY));

        foreach ($query as $queryItem) {
            [$key, $value] = str_contains($queryItem, '=')
                ? explode('=', $queryItem)
                : [$queryItem, null];

            if ($searchKey === $key && !empty($value)) {
                return trim($value);
            }
        }

        return null;
    }
}
