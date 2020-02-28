<?php

namespace Spatie\EventServer\Server\Events;

use ReflectionClass;
use Spatie\EventServer\Client\Client;

class EventBus
{
    private array $subscriptions = [];

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function trigger(object $event): void
    {
        $this->client->event($event);
    }

    public function handle(object $event)
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
