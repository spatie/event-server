<?php

namespace App\Console;

use App\Domain\Account\AccountAggregateRoot;
use App\Domain\Account\Entities\Account;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BalanceAddCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('balance:add');

        $this->logger = $logger;

        $this
            ->addArgument('account', InputArgument::REQUIRED, 'The UUID of the account')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to add');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uuid = $input->getArgument('account');

        $accountAggregateRoot = AccountAggregateRoot::find($uuid);

        $amount = (int) $input->getArgument('amount');

        $accountAggregateRoot->addMoney($amount);

        $account = Account::find($uuid);

        $this->logger->log("Added {$amount} to account {$account->name}, balance is {$account->balance}");

        return 0;
    }
}
