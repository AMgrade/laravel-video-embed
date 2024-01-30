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
### Get parsed video URL data:

```bash
$videoUrlAttributes = VideoEmbedHelper::getVideoUrlAttributes('https://www.youtube.com/shorts/gDEPG9ZIYRY');
```

Example of received data from `$videoUrlAttributes`:

```bash
{
	"id": "lF0-wjogo5w",
	"key": "youtube.com",
	"type": "single",
	"original": "https://www.youtube.com/watch?v=lF0-wjogo5w",
}
```

### Get iframe code for reflection video

```bash
$videoUrlAttributes = VideoEmbedHelper::getVideoUrlAttributes('https://www.youtube.com/shorts/gDEPG9ZIYRY');

$iframeCode = VideoEmbedHelper::getVideoIframeCode($videoUrlAttributes);
```

Example of received data from `$iframeCode`:

```bash
<iframe height="460" width="640" frameborder="0" allow="autoplay; fullscreen; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen="true" allowtransparency="true" src="https://www.youtube.com/embed/gDEPG9ZIYRY?" />
```
