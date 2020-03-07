<?php

use Ramsey\Uuid\Uuid;
use Spatie\EventServer\Container;
use Spatie\EventServer\Domain\Event;

if (! function_exists('uuid')) {
    function uuid(): string
    {
        return (string) Uuid::uuid4();
    }
}

if (! function_exists('dispatch')) {
    function dispatch(Event $event): void
    {
        $eventBus = Container::make()->eventBus();

        $eventBus->dispatch($event);
    }
}

if (! function_exists('runOn')) {
    function runOn(Closure $server, Closure $client)
    {
        if (Container::isServer()) {
            return $server();
        }

        return $client(Container::make()->gateway());
    }
}
