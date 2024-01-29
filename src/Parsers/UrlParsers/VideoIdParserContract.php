<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\UrlParsers;

use const null;

interface VideoIdParserContract
{
    public function parse(array $parsed, string $url): ?array;

    public function validate(string $url): bool;

    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string;
}
