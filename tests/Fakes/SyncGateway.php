<?php

namespace Spatie\EventServer\Tests\Fakes;

use React\Http\Io\ServerRequest;
use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Server\Server;

class SyncGateway extends Gateway
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function event(object $event): void
    {
        $request = (new ServerRequest('POST', $this->uri('events')))
            ->withParsedBody([
                'event' => serialize($event),
            ]);

        $this->server->receive($request);
    }
}
