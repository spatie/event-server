<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Domain\Event;

class IncreaseBalanceEvent extends Event
{
    public int $amount;

    public function __construct(int $amount = 0)
    {
        $this->amount = $amount;
    }

    public function withAmount(int $amount): IncreaseBalanceEvent
    {
        $clone = clone $this;

        $clone->amount = $amount;

        return $clone;
    }
}
