<?php

namespace Spatie\EventServer\Client;

use Spatie\EventServer\Domain\Aggregate;
use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Domain\Payments\Ledger;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;
use Spatie\EventServer\Server\RequestHandlers\GetAggregateHandler;
use Spatie\EventServer\Server\RequestHandlers\TriggerEventHandler;

class Gateway
{
    private SocketClient $client;

    public function __construct(SocketClient $client)
    {
        $this->client = $client;
    }

    public function event(Event $event): void
    {
        if (! isset($event->uuid)) {
            $event->uuid = uuid();
        }

        $this->request(TriggerEventHandler::class, [
            'event' => $event,
        ]);
    }

    public function getAggregate(string $aggregateClass, string $aggregateUuid): Aggregate
    {
        $payload = $this->request(GetAggregateHandler::class, [
            'aggregateClass' => $aggregateClass,
            'aggregateUuid' => $aggregateUuid,
        ]);

        return unserialize($payload->get('aggregate'));
    }

    protected function request(string $handlerClass, array $data): Payload
    {
        $payload = RequestPayload::make($handlerClass, $data);

        return $this->client->send($payload);
    }
}
