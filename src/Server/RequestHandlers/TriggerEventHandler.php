<?php

namespace Spatie\EventServer\Server\RequestHandlers;

use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;

class TriggerEventHandler implements RequestHandler
{
    private EventBus $eventBus;

    private EventStore $eventStore;

    public function __construct(
        EventBus $eventBus,
        EventStore $eventStore
    ) {
        $this->eventBus = $eventBus;
        $this->eventStore = $eventStore;
    }

    public function __invoke(RequestPayload $payload): Payload
    {
        $event = $payload->get('event');

        $this->eventStore->store($event);

        $this->eventBus->handle($event);

        return new Payload();
    }
}
