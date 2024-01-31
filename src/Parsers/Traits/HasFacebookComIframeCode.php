<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Parsers\Traits;

use const null;

trait HasFacebookComIframeCode
{
    /**
     * @see https://developers.facebook.com/docs/plugins/embedded-video-player/
     */
    public function getIframeCode(
        string $id,
        string $original,
        array $urlQuery = [],
        array $attributes = [],
        ?string $type = null,
    ): ?string {
        $url = 'https://www.facebook.com/plugins/video.php';

        $urlQuery['href'] = $original;

        return $this->buildIframeCode($url, $urlQuery);
    }
}
