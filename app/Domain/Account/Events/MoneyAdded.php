<?php

namespace App\Domain\Account\Events;

use Spatie\EventServer\Domain\Event;

final class MoneyAdded extends Event
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
