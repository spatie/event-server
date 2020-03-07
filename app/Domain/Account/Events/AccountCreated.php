<?php

namespace App\Domain\Account\Events;

use Spatie\EventServer\Domain\Event;

final class AccountCreated extends Event
{
    public string $name;

    public int $userId;

    public function __construct(string $name, int $userId)
    {
        $this->name = $name;

        $this->userId = $userId;
    }
}
