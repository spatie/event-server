<?php

namespace App\Domain\Account\Events;

use Spatie\EventServer\Domain\Event;

final class MoneySubtracted extends Event
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
