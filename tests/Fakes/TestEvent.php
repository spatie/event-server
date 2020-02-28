<?php

namespace Spatie\EventServer\Tests\Fakes;

use Spatie\EventServer\Domain\Event;

class TestEvent extends Event
{
    public string $uuid;

    public int $amount = 10;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
}
