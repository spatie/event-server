<?php

namespace App\Domain\Account\Projections;

use App\Domain\Account\Entities\Account;
use App\Domain\Account\Events\AccountCreated;
use App\Domain\Account\Events\AccountDeleted;
use App\Domain\Account\Events\MoneyAdded;
use App\Domain\Account\Events\MoneySubtracted;
use Spatie\EventServer\Domain\Projection;

class AccountProjection extends Projection
{
    public function onAccountCreated(AccountCreated $event): void
    {
        Account::create(
            [
                'uuid' => $event->meta()->aggregateUuid,
                'name' => $event->name,
            ]
        );
    }

    public function onMoneyAdded(MoneyAdded $event): void
    {
        $account = Account::find($event->meta()->aggregateUuid);

        $account->balance += $event->amount;
    }

    public function onMoneySubtracted(MoneySubtracted $event): void
    {
        $account = Account::find($event->meta()->aggregateUuid);

        $account->balance -= $event->amount;
    }

    public function onAccountDeleted(AccountDeleted $event): void
    {
        Account::find($event->meta()->aggregateUuid)->delete();
    }
}
