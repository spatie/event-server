<?php

namespace Spatie\EventServer\Domain;

use ReflectionClass;

abstract class Event
{
    private EventMeta $meta;

    public function meta(): EventMeta
    {
        if (! isset($this->meta)) {
            $this->meta = new EventMeta();
        }

        return $this->meta;
    }

    public function getEventName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    public function getHandlerName(): string
    {
        return 'on' . $this->getEventName();
    }

    public function forAggregate(AggregateRoot $aggregate): self
    {
        $this->meta()->aggregateClass = get_class($aggregate);
        $this->meta()->aggregateUuid = $aggregate->uuid;

        return $this;
    }
}
