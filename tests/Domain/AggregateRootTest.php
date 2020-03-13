<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Tests\Fakes\TestAggregateRoot;
use Spatie\EventServer\Tests\TestCase;

class AggregateRootTest extends TestCase
{
    /** @test */
    public function test_create()
    {
        $ledger = (new TestAggregateRoot())
            ->increase(10)
            ->increase(5);

        $this->assertEquals(15, $ledger->balance);
    }

    /** @test */
    public function test_find()
    {
        $uuid = uuid();

        (new TestAggregateRoot($uuid))->increase(10);

        $aggregate = TestAggregateRoot::find($uuid);

        $this->assertEquals(10, $aggregate->balance);
    }
}
