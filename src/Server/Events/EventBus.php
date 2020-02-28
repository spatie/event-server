<?php

namespace Spatie\EventServer\Server\Events;

use ReflectionClass;
use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Domain\AggregateRepository;
use Spatie\EventServer\Domain\Event;

class EventBus
{
    private Gateway $gateway;

    private array $subscriptions = [];

    private AggregateRepository $aggregateRepository;

    public function __construct(
        Gateway $gateway,
        AggregateRepository $aggregateRepository
    ) {
        $this->gateway = $gateway;
        $this->aggregateRepository = $aggregateRepository;
    }

    public function dispatch(Event $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->gateway->event($event);
    }

    public function handle(Event $event): void
    {
        $this->notifySubscribers($event);

        if ($event->meta()->aggregateUuid) {
            $this->notifyAggregate($event);
        }
    }

    private function notifySubscribers(Event $event): void
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

    private function notifyAggregate(Event $event): void
    {
        $aggregate = $this->aggregateRepository->resolve(
            $event->meta()->aggregateClass,
            $event->meta()->aggregateUuid,
        );

        $aggregate->apply($event);
    }
}
