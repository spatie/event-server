<?php

namespace Spatie\EventServer\Tests\Domain;


use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Domain\Projection;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Tests\TestCase;

class ProjectionTest extends TestCase
{
    private EventBus $eventBus;

    private TestProjection $projection;

    private EventStore $eventStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projection = new TestProjection();

        $this->container->subscribers()->add(
            $this->projection
        );

        $this->eventBus = $this->container->eventBus();

        $this->eventStore = $this->container->eventStore();
    }

    /** @test */
    public function projection_can_subscribe_to_event()
    {
        $this->eventBus->dispatch(new TestProjectionEvent(10));

        $this->assertEquals(10, $this->projection->balance);
    }

    /** @test */
    public function projections_are_replayed()
    {
        $this->storeEvents(
            new TestProjectionEvent(10),
            new TestProjectionEvent(5)
        );

        $this->eventStore->replay();

        $this->assertEquals(15, $this->projection->balance);
    }
}

class TestProjection extends Projection
{
    public int $balance = 0;

    public function onTestProjectionEvent(TestProjectionEvent $event): void
    {
        $this->balance += $event->amount;
    }
}

class TestProjectionEvent extends Event
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
