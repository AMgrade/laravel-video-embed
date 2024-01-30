<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Tests\Unit;

use AMgrade\VideoEmbed\Helpers\VideoEmbedHelper;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testParsers(): void
    {
        $videoEmbedData = require __DIR__ . '/../data/video-embed.php';

        $helper = Container::getInstance()->make(VideoEmbedHelper::class);

        foreach ($videoEmbedData as $key => $value) {
            foreach ($value as $item) {
                $this->assertEquals(
                    $item['expected'],
                    $helper::getVideoUrlAttributes($item['given']),
                );
            }
        }
    }
}
