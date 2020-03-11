<?php

namespace Spatie\EventServer\Tests;

use Spatie\EventServer\Tests\Fakes\TestAggregate;

class RealServerTest extends ServerTestCase
{
    /** @test */
    public function real_server()
    {
        $server = $this->startServer();

        $uuid = uuid();

        $aggregate = new TestAggregate($uuid);

        $aggregate->increase(10);

        $aggregate = TestAggregate::find($uuid);

        $this->assertEquals(10, $aggregate->balance);

        $server->stop();

        $server = $this->startServer();

        $aggregate = TestAggregate::find($uuid);

        $this->assertEquals(10, $aggregate->balance);

        $server->stop();
    }
}
