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

You can add the keys of parsers that need to be used in video URL parsing in section `video-parsers`, `keys` in `config/video-embed.php`.

For getting parsed video information call `getVideoUrlAttributes` method from `VideoIdParserHelper` and pass video URL.

Example of received data from a parsed video URL: 

```bash
{
	"id": "lF0-wjogo5w",
	"key": "youtube.com",
	"type": "single",
	"original": "https://www.youtube.com/watch?v=lF0-wjogo5w"
}
```

For getting video iframe code call `getVideoIframeCode` method and pass result into `getVideoUrlAttributes` method from `VideoIdParserHelper` for getting iframe tag with information for reflection video.
