<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Server\Server;
use Throwable;

class SyncServer extends Server
{
    public function run(): void
    {
        // Nothing needs to happen here
    }

    public function handleRequestError(Throwable $throwable)
    {
        throw $throwable;
    }
}
