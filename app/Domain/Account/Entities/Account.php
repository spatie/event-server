<?php

namespace App\Domain\Account\Entities;

use Spatie\EventServer\Domain\Entity;

class Account extends Entity
{
    public string $uuid;

    public string $name;

    public int $balance = 0;
}
