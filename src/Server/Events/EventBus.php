<?php

namespace Spatie\EventServer\Server\Events;

use Spatie\EventServer\Client\Gateway;
use Spatie\EventServer\Domain\AggregateRepository;
use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Domain\Reactor;
use Spatie\EventServer\Domain\Subscribers;

class EventBus
{
    private Gateway $gateway;

    private AggregateRepository $aggregateRepository;

    private Subscribers $subscribers;

    public function __construct(
        Gateway $gateway,
        AggregateRepository $aggregateRepository,
        Subscribers $subscribers
    ) {
        $this->gateway = $gateway;
        $this->aggregateRepository = $aggregateRepository;
        $this->subscribers = $subscribers;
    }

    public function dispatch(Event $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->gateway->event($event);
    }

    public function handle(Event $event, bool $replay = false): void
    {
        $this->notifySubscribers($event, $replay);

        if ($event->meta()->aggregateUuid) {
            $this->notifyAggregate($event);
        }
    }

    private function notifySubscribers(Event $event, bool $replay = false): void
    {
        foreach ($this->subscribers as $subscriber) {
            if ($replay && $subscriber instanceof Reactor) {
                continue;
            }

            if (! $subscriber->subscribesTo($event)) {
                continue;
            }

            $subscriber->handle($event);
        }
    }

    private function notifyAggregate(Event $event): void
    {
        $aggregate = $this->aggregateRepository->resolve(
            $event->meta()->aggregateClass,
            $event->meta()->aggregateUuid
        );

        $aggregate->apply($event);
    }
}
