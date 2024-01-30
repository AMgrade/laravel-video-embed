<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Tests\Unit;

use AMgrade\VideoEmbed\Helpers\VideoEmbedHelper;
use AMgrade\VideoEmbed\Tests\TestCase;

class VideoEmbedTest extends TestCase
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testParsers(): void
    {
        $videoEmbedData = require __DIR__ . '/../data/video-embed.php';

        foreach ($videoEmbedData as $key => $value) {
            foreach ($value as $item) {
                $this->assertEquals(
                    $item['expected'],
                    VideoEmbedHelper::getVideoUrlAttributes($item['given']),
                );
            }
        }
    }
}
