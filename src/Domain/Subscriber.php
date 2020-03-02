<?php

namespace Spatie\EventServer\Domain;

use Closure;

abstract class Subscriber
{
    public function subscribesTo(Event $event): bool
    {
        return method_exists($this, "on{$event->getEventName()}");
    }

    public function getEventHandler(Event $event): Closure
    {
        return Closure::fromCallable([$this, "on{$event->getEventName()}"]);
    }

    /**
     * @param \Spatie\EventServer\Domain\Event $event
     *
     * @return mixed
     */
    public function handle(Event $event)
    {
        $handler = $this->getEventHandler($event);

        return $handler($event);
    }
}
