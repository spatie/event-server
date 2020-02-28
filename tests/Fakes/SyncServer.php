<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Server\Server;
use Throwable;

class SyncServer extends Server
{
    public function startServer(): void
    {
        // Nothing needs to happen here
    }

    public function handleRequestError(Throwable $throwable): void
    {
        throw $throwable;
    }
}
