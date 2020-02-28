<?php

namespace Spatie\EventServer\Tests\Domain;

use Spatie\EventServer\Domain\Payments\Ledger;
use Spatie\EventServer\Tests\TestCase;

class AggregateTest extends TestCase
{
    /** @test */
    public function test()
    {
        $ledger = Ledger::create(10)
            ->add(10)
            ->subtract(5);

        $this->assertEquals(15, $ledger->balance);
    }
}
