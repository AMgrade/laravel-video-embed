<?php

declare(strict_types=1);

namespace Tests\Unit;

use AMgrade\VideoEmbed\Parsers\VideoParser;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testParsers(): void
    {
        $videoIdParserData = require __DIR__ . '/../data/video-embed.php';

        $parser = Container::getInstance()->make(VideoParser::class);

        foreach ($videoIdParserData as $key => $value) {
            foreach ($value as $item) {
                $this->assertEquals(
                    $item['expected'],
                    $parser->parse($item['given'], [$key]),
                );
            }
        }
    }
}
