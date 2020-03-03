<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Domain\Aggregate;

class TestAggregate extends Aggregate
{
    public int $balance = 0;

    public function increase(int $amount): self
    {
        $this->event(new IncreaseBalanceEvent($amount));

        return $this;
    }

    public function onIncreaseBalanceEvent(IncreaseBalanceEvent $event)
    {
        $this->balance += $event->amount;
    }
}
