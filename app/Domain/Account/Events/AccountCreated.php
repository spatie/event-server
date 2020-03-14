<?php

namespace App\Domain\Account\Events;

use Spatie\EventServer\Domain\Event;

final class AccountCreated extends Event
{
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
