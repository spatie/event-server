<?php

namespace Spatie\EventServer\Tests\Fakes;

use Psr\Http\Message\ResponseInterface;
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

    protected function request(string $verb, string $uri, array $body): ResponseInterface
    {
        $request = (new ServerRequest($verb, $uri))->withParsedBody($body);

        return $this->server->receive($request);
    }
}
