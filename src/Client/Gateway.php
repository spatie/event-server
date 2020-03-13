<?php

namespace Spatie\EventServer\Client;

use Spatie\EventServer\Domain\AggregateRoot;
use Spatie\EventServer\Domain\Entity;
use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Server\ExceptionPayload;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestHandlers\Entities\CreateEntityHandler;
use Spatie\EventServer\Server\RequestHandlers\Entities\DeleteEntityHandler;
use Spatie\EventServer\Server\RequestHandlers\Entities\FindEntityHandler;
use Spatie\EventServer\Server\RequestHandlers\Entities\ListEntitiesHandler;
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

    public function listEntities(string $entityClass): array
    {
        $payload = $this->request(ListEntitiesHandler::class, [
            'entityClass' => $entityClass,
        ]);

        return $payload->get('entities');
    }

    public function findEntity(string $entityClass, string $uuid): Entity
    {
        $payload = $this->request(FindEntityHandler::class, [
            'entityClass' => $entityClass,
            'uuid' => $uuid,
        ]);

        return $payload->get('entity');
    }

    public function createEntity(Entity $entity): void
    {
        $this->request(CreateEntityHandler::class, [
            'entity' => $entity,
        ]);
    }

    public function deleteEntity(Entity $entity): void
    {
        $this->request(DeleteEntityHandler::class, [
            'entity' => $entity,
        ]);
    }

    public function getAggregate(string $aggregateClass, string $aggregateUuid): AggregateRoot
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

        $responsePayload = $this->client->send($payload);

        if ($responsePayload instanceof ExceptionPayload) {
            throw $responsePayload->toException();
        }

        return $responsePayload;
    }
}
