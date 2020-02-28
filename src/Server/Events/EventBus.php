<?php

namespace Spatie\EventServer\Server\Events;

use ReflectionClass;
use Spatie\EventServer\Client\Gateway;

class EventBus
{
    private EventStore $eventStore;

    private Gateway $gateway;

    private array $subscriptions = [];

    public function __construct(
        EventStore $eventStore,
        Gateway $client
    ) {
        $this->eventStore = $eventStore;
        $this->gateway = $client;
    }

    public function dispatch(object $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->gateway->event($event);
    }

    public function handle(object $event): void
    {
        $this->storeEvent($event);

        $this->notifySubscribers($event);
    }

    private function storeEvent(object $event): void
    {
        $this->eventStore->store($event);
    }

    private function notifySubscribers(object $event): void
    {
        $eventClassName = get_class($event);

        $eventName = (new ReflectionClass($event))->getShortName();

        foreach (($this->subscriptions[$eventClassName] ?? []) as $subscriber) {
            $handler = "on{$eventName}";

            if (! method_exists($subscriber, $handler)) {
                continue;
            }

            $subscriber->$handler($event);
        }
    }
}
