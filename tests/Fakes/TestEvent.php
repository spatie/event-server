<?php

namespace Spatie\EventServer\Tests\Fakes;

class TestEvent
{
    public string $uuid;

    public int $amount = 10;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
}
