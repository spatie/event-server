<?php

namespace Spatie\EventServer\Tests\Domain;


use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Domain\Reactor;
use Spatie\EventServer\Server\Events\EventBus;
use Spatie\EventServer\Server\Events\EventStore;
use Spatie\EventServer\Tests\TestCase;

class ReactorTest extends TestCase
{
    private EventBus $eventBus;

    private TestReactor $reactor;

    private EventStore $eventStore;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reactor = new TestReactor();

        $this->container->subscribers()->add(
            $this->reactor
        );

        $this->eventBus = $this->container->eventBus();

        $this->eventStore = $this->container->eventStore();
    }

    /** @test */
    public function reactor_can_subscribe_to_event()
    {
        $this->eventBus->dispatch(new TestReactorEvent(10));

        $this->assertEquals(10, $this->reactor->balance);
    }

    /** @test */
    public function reactors_are_not_replayed()
    {
        $this->storeEvents(
            new TestReactorEvent(10),
            new TestReactorEvent(5)
        );

        $this->eventStore->replay();

        $this->assertEquals(0, $this->reactor->balance);
    }
}

class TestReactor extends Reactor
{
    public int $balance = 0;

    public function onTestReactorEvent(TestReactorEvent $eventA): void
    {
        $this->balance += $eventA->amount;
    }
}

class TestReactorEvent extends Event
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
