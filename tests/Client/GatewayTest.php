<?php

namespace Spatie\EventServer\Tests\Client;

use Spatie\EventServer\Domain\Entity;
use Spatie\EventServer\Tests\Fakes\IncreaseBalanceEvent;
use Spatie\EventServer\Tests\Fakes\TestAggregate;
use Spatie\EventServer\Tests\TestCase;

class GatewayTest extends TestCase
{
    /** @test */
    public function get_aggregate()
    {
        $repository = $this->container->aggregateRepository();

        $originalAggregate = $repository->resolve(TestAggregate::class, uuid());

        $aggregateFromServer = $this->gateway->getAggregate(TestAggregate::class, $originalAggregate->uuid);

        $this->assertInstanceOf(TestAggregate::class, $aggregateFromServer);
    }

    /** @test */
    public function get_aggregate_with_replayed_events()
    {
        $aggregate = new TestAggregate();

        $event = (new IncreaseBalanceEvent())->forAggregate($aggregate);

        $this->storeEvents(
            $event->withAmount(10),
            $event->withAmount(5),
            $event->withAmount(1),
        );

        $this->server->run();

        /** @var TestAggregate $aggregateFromServer */
        $aggregateFromServer = $this->gateway->getAggregate(TestAggregate::class, $aggregate->uuid);

        $this->assertInstanceOf(TestAggregate::class, $aggregateFromServer);
        $this->assertEquals(16, $aggregateFromServer->balance);
        $this->assertEquals(3, $aggregateFromServer->version);
    }

    /** @test */
    public function test_entities()
    {
        $entityA = new TestGatewayEntity('a');
        $entityB = new TestGatewayEntity('b');

        $this->server->run();

        $this->gateway->createEntity($entityA);
        $this->gateway->createEntity($entityB);

        $entities = $this->gateway->listEntities(TestGatewayEntity::class);

        $this->assertCount(2, $entities);

        $this->assertEquals($entityA->uuid, $entities[0]->uuid);
        $this->assertEquals($entityB->uuid, $entities[1]->uuid);

        $entityFromServer = $this->gateway->findEntity($entityA->getClass(), $entityA->uuid);

        $this->assertEquals($entityA->uuid, $entityFromServer->uuid);
        $this->assertEquals($entityA->description, $entityFromServer->description);

        $this->gateway->deleteEntity($entityB);

        $entities = $this->gateway->listEntities(TestGatewayEntity::class);

        $this->assertCount(1, $entities);
    }
}

class TestGatewayEntity extends Entity {
    public string $description;

    public function __construct(string $description)
    {
        parent::__construct();

        $this->description = $description;
    }
}
