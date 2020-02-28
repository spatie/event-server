<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Spatie\EventServer\Server\Events\EventBus;

class EventRequestHandler implements RequestHandler
{
    private EventBus $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $event = unserialize($parsedBody['event']);

        $this->eventBus->handle($event);

        return new Response(200);
    }
}
