<?php

namespace App;

use App\Domain\Account\Projections\AccountProjection;
use App\Domain\Account\Reactors\OfferLoanReactor;
use Spatie\EventServer\Console\ConsoleApplication;
use Spatie\EventServer\Container as BaseContainer;
use Spatie\EventServer\Domain\Subscribers;
use Symfony\Component\Finder\Finder;

class Container extends BaseContainer
{
    public function consoleApplication(): ConsoleApplication
    {
        $application = parent::consoleApplication();

        /** @var \SplFileInfo[] $commands */
        $commands = Finder::create()->in(__DIR__ . '/Console')->name('*Command.php');

        foreach ($commands as $commandClass) {
            $className = "\\App\\Console\\{$commandClass->getBasename('.php')}";

            $command = $this->resolve($className);

            $application->addCommands([$command]);
        }

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
