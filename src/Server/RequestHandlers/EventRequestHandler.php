<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

class EventRequestHandler implements RequestHandler
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $event = unserialize($parsedBody['event']);

        dump($event);

        return new Response(200);
    }
}
