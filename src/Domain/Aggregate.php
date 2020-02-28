<?php

namespace Spatie\EventServer\Domain;

use ReflectionClass;
use Spatie\EventServer\Container;

abstract class Aggregate
{
    public string $uuid;

    public int $version = 0;

    /**
     * @return \Spatie\EventServer\Domain\Aggregate|static
     */
    public static function new(): Aggregate
    {
        return new static();
    }

    protected function event(object $event): self
    {
        $eventBus = Container::make()->eventBus();

        $eventBus->trigger($event);

        $this->apply($event);

        $this->version++;

        return $this;
    }

    public function apply(object $event): self
    {
        $eventName = (new ReflectionClass($event))->getShortName();

        $handler = "on{$eventName}";

        if (! method_exists($this, $handler)) {
            return $this;
        }

        $this->$handler($event);

        return $this;
    }
}
