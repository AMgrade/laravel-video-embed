<?php

declare(strict_types = 1);

namespace AMgrade\VideoEmbed\Tests;

use AMgrade\VideoEmbed\VideoEmbedServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [VideoEmbedServiceProvider::class];
    }
}
