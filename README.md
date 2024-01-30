# Laravel Video Embed

## About Laravel Video Embed

Laravel Video Embed is a package that allows you to get iframe code by video URL.

## Installation

```bash
composer require amgrade/laravel-video-embed
```

## Configuration
Add to `config/app.php` into `providers` section next line:

```php
'providers' => [
    AMgrade\VideoEmbed\VideoEmbedServiceProvider::class,
],
```

For publishing config, run the next command:

```bash
php artisan video-embed:publish
```

For configuring video iframe open `config/video-embed.php` and add or change height and width for video parser(s).

You can add the keys of parsers that need to be used in video URL parsing in section `video-parsers` in `config/video-embed.php`.

## Usage
### Get iframe code for reflection video:

```php
<?php

declare(strict_types=1);

use AMgrade\VideoEmbed\Helpers\VideoEmbedHelper;

require __DIR__.'/vendor/autoload.php';

// Get parsed video URL data.
$videoUrlAttributes = VideoEmbedHelper::getVideoUrlAttributes('https://www.youtube.com/shorts/gDEPG9ZIYRY');

// Get iframe code.
$iframeCode = VideoEmbedHelper::getVideoIframeCode($videoUrlAttributes);
```
