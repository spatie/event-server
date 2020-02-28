<?php

namespace Spatie\EventServer\Domain\Payments\Events;

class CreateLedgerEvent
{
    public string $uuid;

    public int $balance;

    public function __construct(int $balance)
    {
        $this->uuid = uuid();
        $this->balance = $balance;
    }
}
