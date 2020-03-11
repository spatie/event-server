<?php

namespace App;

use App\Console\BalanceAddCommand;
use App\Console\BalanceSubtractCommand;
use App\Console\CreateAccountCommand;
use App\Console\ListAccountsCommand;
use App\Domain\Account\Projections\AccountProjection;
use App\Domain\Account\Reactors\OfferLoanReactor;
use Spatie\EventServer\Console\ConsoleApplication;
use Spatie\EventServer\Container as BaseContainer;
use Spatie\EventServer\Domain\Subscribers;

class Container extends BaseContainer
{
    public function consoleApplication(): ConsoleApplication
    {
        $application = parent::consoleApplication();

        $application->addCommands([
            new ListAccountsCommand($this->logger()),
            new CreateAccountCommand($this->logger()),
            new BalanceAddCommand($this->logger()),
            new BalanceSubtractCommand($this->logger()),
        ]);

        return $application;
    }

    public function subscribers(): Subscribers
    {
        return parent::subscribers()
            ->add($this->offerLoanReactor())
            ->add($this->accountProjection());
    }

    public function offerLoanReactor(): OfferLoanReactor
    {
        return $this->singleton(OfferLoanReactor::class, fn() => new OfferLoanReactor(
            $this->logger(),
        ));
    }

    public function accountProjection(): AccountProjection
    {
        return $this->singleton(AccountProjection::class, fn() => new AccountProjection());
    }
}
