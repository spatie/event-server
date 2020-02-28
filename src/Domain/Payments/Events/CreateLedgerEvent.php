<?php

namespace Spatie\EventServer\Domain\Payments\Events;

use Spatie\EventServer\Domain\Event;

class CreateLedgerEvent extends Event
{
    public string $uuid;

    public int $balance;

    public function __construct(int $balance)
    {
        $this->uuid = uuid();
        $this->balance = $balance;
    }
}
