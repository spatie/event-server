<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Console\Logger;

class FakeLogger extends Logger
{
    public function __construct()
    {
        // Nothing needs to happen here
    }

    public function log(string $line): void
    {
        return;
    }
}
