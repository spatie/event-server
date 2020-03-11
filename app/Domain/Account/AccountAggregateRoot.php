<?php

namespace App\Domain\Account;

use App\Domain\Account\Events\AccountCreated;
use App\Domain\Account\Events\AccountDeleted;
use App\Domain\Account\Events\AccountLimitHit;
use App\Domain\Account\Events\MoneyAdded;
use App\Domain\Account\Events\MoneySubtracted;
use App\Domain\Account\Events\MoreMoneyNeeded;
use App\Domain\Account\Exceptions\CouldNotSubtractMoney;
use Spatie\EventServer\Domain\Aggregate;

class AccountAggregateRoot extends Aggregate
{
    private int $balance = 0;

    private int $accountLimit = -5000;

    private int $accountLimitHitInARow = 0;

    public function createAccount(string $name, string $userId): self
    {
        $this->event(new AccountCreated($name, $userId));

        return $this;
    }

    public function addMoney(int $amount): self
    {
        $this->event(new MoneyAdded($amount));

        return $this;
    }

    protected function applyMoneyAdded(MoneyAdded $event): void
    {
        $this->accountLimitHitInARow = 0;

        $this->balance += $event->amount;
    }

    public function subtractMoney(int $amount): self
    {
        if (! $this->hasSufficientFundsToSubtractAmount($amount)) {
            $this->event(new AccountLimitHit());

            if ($this->needsMoreMoney()) {
                $this->event(new MoreMoneyNeeded());
            }

            throw CouldNotSubtractMoney::notEnoughFunds($amount);
        }

        $this->event(new MoneySubtracted($amount));

        return $this;
    }

    protected function applyMoneySubtracted(MoneySubtracted $event): void
    {
        $this->balance -= $event->amount;

        $this->accountLimitHitInARow = 0;
    }

    public function deleteAccount(): self
    {
        $this->event(new AccountDeleted());

        return $this;
    }

    public function applyAccountLimitHit(): void
    {
        $this->accountLimitHitInARow++;
    }

    private function hasSufficientFundsToSubtractAmount(int $amount): bool
    {
        return $this->balance - $amount >= $this->accountLimit;
    }

    private function needsMoreMoney(): bool
    {
        return $this->accountLimitHitInARow >= 3;
    }
}
