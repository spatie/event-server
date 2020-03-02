<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Container;
use Spatie\EventServer\Server\Server;

class TestContainer extends Container
{
    public function logger(): Logger
    {
        return new FakeLogger();
    }

    public function server(): Server
    {
        return new SyncServer(
            $this->loop(),
            $this->logger(),
            $this->eventStore(),
            $this->config->listen
        );
    }

    public function gateway(): Gateway
    {
        return new SyncGateway($this->server());
    }
}
