<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Domain\Event;
use Spatie\EventServer\Domain\Subscriber;
use Spatie\EventServer\Tests\TestCase;

class SubscriberTest extends TestCase
{
    /** @test */
    public function test_subscribes_to()
    {
        $subscriber = new TestSubscriber();

        $this->assertTrue($subscriber->subscribesTo(new TestSubscriberEventA()));
        $this->assertFalse($subscriber->subscribesTo(new TestSubscriberEventB()));
    }

    /** @test */
    public function test_handle()
    {
        $subscriber = new TestSubscriber();

        $result = $subscriber->handle(new TestSubscriberEventA());

        $this->assertEquals(1, $result);
    }
}

class TestSubscriber extends Subscriber
{
    public function onTestSubscriberEventA(TestSubscriberEventA $event): int
    {
        return 1;
    }
}

class TestSubscriberEventA extends Event
{
}

class TestSubscriberEventB extends Event
{
}
