<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Config;
use Spatie\EventServer\Server\Events\FileEventStore;

class TestConfig extends Config
{
    public ?string $storagePath = __DIR__ . '/../.storage';

    public string $eventStore = FileEventStore::class;
}
