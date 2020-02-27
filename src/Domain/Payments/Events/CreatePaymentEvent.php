<?php

namespace Spatie\EventServer\Domain\Payments\Events;

class CreatePaymentEvent
{
    public string $uuid;

    public int $amount;

    public function __construct(int $amount)
    {
        $this->uuid = uuid();
        $this->amount = $amount;
    }
}
