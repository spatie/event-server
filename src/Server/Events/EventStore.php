<?php

namespace Spatie\EventServer\Server\Events;

use Spatie\EventServer\Domain\Event;

abstract class EventStore
{
    protected EventBus $eventBus;

    public function setEventBus(EventBus $eventBus): self
    {
        $this->eventBus = $eventBus;

        return $this;
    }

    abstract public function store(Event $event): void;

    abstract public function replay(): void;
}
