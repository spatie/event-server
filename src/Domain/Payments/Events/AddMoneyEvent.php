<?php

namespace Spatie\EventServer\Domain\Payments\Events;

use Spatie\EventServer\Domain\Event;

class AddMoneyEvent extends Event
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
