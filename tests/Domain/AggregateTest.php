<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Tests\Fakes\TestAggregate;
use Spatie\EventServer\Tests\TestCase;

class AggregateTest extends TestCase
{
    /** @test */
    public function test()
    {
        $ledger = (new TestAggregate())
            ->increase(10)
            ->increase(5);

        $this->assertEquals(15, $ledger->balance);
    }
}
