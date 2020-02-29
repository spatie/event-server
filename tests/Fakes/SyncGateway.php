<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;
use Spatie\EventServer\Server\Server;

class SyncGateway extends Gateway
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    protected function request(string $handlerClass, array $data): Payload
    {
        $payload = RequestPayload::make($handlerClass, $data);

        return $this->server->receive($payload);
    }
}
