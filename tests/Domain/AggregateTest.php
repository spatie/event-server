<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Tests\Fakes\TestAggregate;
use Spatie\EventServer\Tests\TestCase;

class AggregateTest extends TestCase
{
    /** @test */
    public function test_create()
    {
        $ledger = (new TestAggregate())
            ->increase(10)
            ->increase(5);

        $this->assertEquals(15, $ledger->balance);
    }

    /** @test */
    public function test_find()
    {
        $uuid = uuid();

        (new TestAggregate($uuid))->increase(10);

        $aggregate = TestAggregate::find($uuid);

        $this->assertEquals(10, $aggregate->balance);
    }
}
