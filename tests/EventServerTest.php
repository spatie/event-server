<?php

namespace Spatie\EventServer\Tests;

use Carbon\Carbon;
use Spatie\EventServer\Tests\Fakes\TestEvent;

class EventServerTest extends TestCase
{
    /** @test */
    public function event_is_stored()
    {
        $server = $this->container->server();

        $server->run();

        $gateway = $this->container->gateway();

        $event = new TestEvent('uuid');

        $gateway->event($event);

        $this->assertEventStored($event);
    }

    /** @test */
    public function stored_events_are_loaded_on_startup()
    {
        $this->storeEvents(
            new TestEvent('1'),
            new TestEvent('2'),
            new TestEvent('3'),
        );

        $server = $this->container->server();

        $server->run();
    }
}
