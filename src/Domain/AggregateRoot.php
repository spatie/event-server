<?php

namespace Spatie\EventServer\Domain;

use Spatie\EventServer\Container;

abstract class AggregateRoot
{
    public string $uuid;

    public int $version = 0;

    /**
     * @return \Spatie\EventServer\Domain\AggregateRoot|static
     */
    public static function new(?string $uuid = null): AggregateRoot
    {
        return new static($uuid);
    }

    public function __construct(?string $uuid = null)
    {
        $this->uuid = $uuid ?? uuid();
    }

    /**
     * @param string $uuid
     *
     * @return \Spatie\EventServer\Domain\AggregateRoot|static
     */
    public static function find(string $uuid): AggregateRoot
    {
        return Container::make()->gateway()->getAggregate(static::class, $uuid);
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
