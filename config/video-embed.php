<?php

declare(strict_types=1);

use AMgrade\VideoEmbed\Parsers\FacebookComParser;
use AMgrade\VideoEmbed\Parsers\InstagramComParser;
use AMgrade\VideoEmbed\Parsers\TikTokComParser;
use AMgrade\VideoEmbed\Parsers\TwitchTVParser;
use AMgrade\VideoEmbed\Parsers\VimeoComParser;
use AMgrade\VideoEmbed\Parsers\YoutubeComParser;
use AMgrade\VideoEmbed\Parsers\YoutuBeParser;

return [
    // List of settings for video iframes.
    'iframe' => [
        'default' => [
            'attributes' => [
                'height' => 460,
                'width' => 640,
            ],
        ],

        FacebookComParser::KEY => [
            'query' => [
                'height' => 600,
                'width' => 700,
                'show_text' => false,
                't' => 0,
            ],
        ],

        InstagramComParser::KEY => [
            'attributes' => [
                'height' => 800,
                'width' => 450,
            ],
        ],

        TikTokComParser::KEY => [
            'attributes' => [
                'height' => 800,
                'width' => 450,
            ],
        ],

        TwitchTVParser::KEY => [
            'query' => [
                'parent' => parse_url(env('APP_URL', 'localhost'), PHP_URL_HOST),
            ],
        ],
    ],

    // The list of video parsers that would be used for parse video Urls.
    'video-parsers' => [
        VimeoComParser::KEY => VimeoComParser::class,
        YoutubeComParser::KEY => YoutubeComParser::class,
        YoutuBeParser::KEY => YoutuBeParser::class,
    ],
];
