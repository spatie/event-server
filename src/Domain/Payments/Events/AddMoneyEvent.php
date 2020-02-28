<?php

namespace Spatie\EventServer\Domain\Payments\Events;

class AddMoneyEvent
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
