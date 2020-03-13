<?php

namespace Spatie\EventServer\Tests\Client;

use Spatie\EventServer\Domain\Entity;
use Spatie\EventServer\Tests\Fakes\IncreaseBalanceEvent;
use Spatie\EventServer\Tests\Fakes\TestAggregateRoot;
use Spatie\EventServer\Tests\TestCase;

class GatewayTest extends TestCase
{
    /** @test */
    public function get_aggregate()
    {
        $repository = $this->container->aggregateRepository();

        $originalAggregate = $repository->resolve(TestAggregateRoot::class, uuid());

        $aggregateFromServer = $this->gateway->getAggregate(TestAggregateRoot::class, $originalAggregate->uuid);

        $this->assertInstanceOf(TestAggregateRoot::class, $aggregateFromServer);
    }

    /** @test */
    public function get_aggregate_with_replayed_events()
    {
        $aggregate = new TestAggregateRoot();

        $event = (new IncreaseBalanceEvent())->forAggregate($aggregate);

        $this->storeEvents(
            $event->withAmount(10),
            $event->withAmount(5),
            $event->withAmount(1),
        );

        $this->server->run();

        /** @var TestAggregateRoot $aggregateFromServer */
        $aggregateFromServer = $this->gateway->getAggregate(TestAggregateRoot::class, $aggregate->uuid);

        $this->assertInstanceOf(TestAggregateRoot::class, $aggregateFromServer);
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
