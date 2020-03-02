<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Config;

class TestConfig extends Config
{
    public ?string $storagePath = __DIR__ . '/../.storage';

    public function subscribers(): array
    {
        return parent::subscribers();
    }
}
