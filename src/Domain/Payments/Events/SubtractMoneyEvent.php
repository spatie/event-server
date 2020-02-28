<?php

namespace Spatie\EventServer\Domain\Payments\Events;

class SubtractMoneyEvent
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
