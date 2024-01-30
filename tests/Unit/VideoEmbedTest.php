<?php

declare(strict_types=1);

namespace Tests\Unit;

use AMgrade\VideoEmbed\Parsers\VideoParser;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testParsers(): void
    {
        $videoEmbedData = require __DIR__ . '/../data/video-embed.php';

        $parser = Container::getInstance()->make(VideoParser::class);

        foreach ($videoEmbedData as $key => $value) {
            foreach ($value as $item) {
                $this->assertEquals(
                    $item['expected'],
                    $parser->parse($item['given'], [$key]),
                );
            }
        }
    }
}
