<?php

namespace Spatie\EventServer\Domain;

abstract class Aggregate
{
    public string $uuid;

    public int $version = 0;

    /**
     * @return \Spatie\EventServer\Domain\Aggregate|static
     */
    public static function new(?string $uuid = null): Aggregate
    {
        return new static($uuid);
    }

    public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid ?? uuid();
    }

    protected function event(Event $event): self
    {
        $event = $event->forAggregate($this);

        dispatch($event);

        $this->apply($event);

        return $this;
    }

    public function apply(Event $event): self
    {
        $this->version++;

        $handler = $event->getHandlerName();

        if (! method_exists($this, $handler)) {
            return $this;
        }

        $this->$handler($event);

        return $this;
    }
}
