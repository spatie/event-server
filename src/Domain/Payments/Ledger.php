<?php

namespace Spatie\EventServer\Domain\Payments;

use Spatie\EventServer\Domain\Aggregate;
use Spatie\EventServer\Domain\Payments\Events\AddMoneyEvent;
use Spatie\EventServer\Domain\Payments\Events\CreateLedgerEvent;
use Spatie\EventServer\Domain\Payments\Events\SubtractMoneyEvent;

class Ledger extends Aggregate
{
    public int $balance = 0;

    public static function create(int $balance): self
    {
        return static::new()->event(new CreateLedgerEvent($balance));
    }

    public function onCreateLedgerEvent(CreateLedgerEvent $createLedgerEvent): self
    {
        $this->balance = $createLedgerEvent->balance;

        return $this;
    }

    public function add(int $amount): self
    {
        return $this->event(new AddMoneyEvent($amount));
    }

    public function onAddMoneyEvent(AddMoneyEvent $event): self
    {
        $this->balance += $event->amount;

        return $this;
    }

    public function subtract(int $amount): self
    {
        return $this->event(new SubtractMoneyEvent($amount));
    }

    public function onSubtractMoneyEvent(SubtractMoneyEvent $event): self
    {
        $this->balance -= $event->amount;

        return $this;
    }
}
