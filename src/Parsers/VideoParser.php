<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

use function array_keys;
use function array_merge;
use function parse_url;

use const false;
use const null;
use const true;

class VideoParser
{
    protected ?array $parsers = null;

    protected array $resolvedParsers = [];

    public function parse(?string $url, array $keys = []): ?array
    {
        if (null === $url) {
            return null;
        }

        $parsedUrl = parse_url($url);

        if (null === ($parsedUrl['host'] ?? null)) {
            return null;
        }

        foreach ($this->getParsers($keys) as $parser) {
            if (null !== ($result = $parser->parse($parsedUrl, $url))) {
                return array_merge($result, ['original' => $url]);
            }
        }

        return null;
    }

    public function validate(?string $url, array $keys = []): bool
    {
        if (null === $url) {
            return false;
        }

        foreach ($this->getParsers($keys) as $parser) {
            if ($parser->validate($url)) {
                return true;
            }
        }

        return false;
    }

    public function getIframeCode(
        string $key,
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        return $this->getParser($key)->getIframeCode(
            $id,
            $original,
            $urlQuery,
            $attributes,
            $type,
        );
    }

    /**
     * @return \AMgrade\VideoEmbed\Parsers\VideoParserContract[]
     */
    protected function getParsers(array $keys = []): array
    {
        if (null === $this->parsers) {
            $this->parsers = Config::get('video-embed.video-parsers', []);
        }

        $parsers = [];

        foreach ($keys ?: array_keys($this->parsers) as $key) {
            $parsers[] = $this->getParser($key);
        }

        return $parsers;
    }

    protected function getParser(string $key): VideoParserContract
    {
        if (!isset($this->parsers[$key])) {
            throw new InvalidArgumentException(
                "Provided parser key {$key} is invalid",
            );
        }

        if (!isset($this->resolvedParsers[$key])) {
            $this->resolvedParsers[$key] = new $this->parsers[$key]();
        }

        return $this->resolvedParsers[$key];
    }
}
