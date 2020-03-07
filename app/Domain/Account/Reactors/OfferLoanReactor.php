<?php

namespace App\Domain\Account\Reactors;

use App\Domain\Account\Entities\Account;
use App\Domain\Account\Events\MoreMoneyNeeded;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Domain\Reactor;

class OfferLoanReactor extends Reactor
{
    private Logger $logger;

    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    public function onMoreMoneyNeeded(MoreMoneyNeeded $event)
    {
        $account = Account::find($event->meta()->aggregateUuid);

        $this->logger->prefix('mail')->error("Loan proposal for {$account->name}");
    }
}
