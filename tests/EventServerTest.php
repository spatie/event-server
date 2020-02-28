<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Tests\Fakes\IncreaseBalanceEvent;
use Spatie\EventServer\Tests\Fakes\TestAggregate;
use Spatie\EventServer\Tests\Fakes\TestEvent;

class EventServerTest extends TestCase
{
    /** @test */
    public function event_is_stored()
    {
        $this->server->run();

        $event = new TestEvent('uuid');

        $this->gateway->event($event);

        $this->assertEventStored($event);
    }

    /** @test */
    public function stored_events_are_loaded_on_startup()
    {
        $aggregate = new TestAggregate();

        $event = (new IncreaseBalanceEvent())->forAggregate($aggregate);

        $this->storeEvents(
            $event->withAmount(10),
            $event->withAmount(5),
            $event->withAmount(1),
        );

        $this->server->run();

        $repository = $this->container->aggregateRepository();

        /** @var TestAggregate $storedAggregate */
        $storedAggregate = $repository->resolve(
            get_class($aggregate),
            $aggregate->uuid
        );

        $this->assertEquals(16, $storedAggregate->balance);
        $this->assertEquals(3, $storedAggregate->version);
    }
}
