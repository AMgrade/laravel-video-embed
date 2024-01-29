<?php

declare(strict_types=1);

namespace AMgrade\VideoEmbed\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'video-embed:publish {--force : Overwrite any existing files}';

    protected $description = 'Publish all of the Video embed parser resources';

    public function handle(): int
    {
        return $this->call('vendor:publish', [
            '--tag' => 'video-embed-config',
            '--force' => $this->option('force'),
        ]);
    }
}
