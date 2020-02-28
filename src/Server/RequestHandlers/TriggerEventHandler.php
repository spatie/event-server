<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\FileEventStore;

class TriggerEventHandler implements RequestHandler
{
    private EventBus $eventBus;

    private FileEventStore $eventStore;

    public function __construct(
        EventBus $eventBus,
        FileEventStore $eventStore
    ) {
        $this->eventBus = $eventBus;
        $this->eventStore = $eventStore;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $event = unserialize($parsedBody['event']);

        $this->eventStore->store($event);

        $this->eventBus->handle($event);

        return new Response(200);
    }
}
