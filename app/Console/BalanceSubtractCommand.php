<?php

namespace App\Console;

use App\Domain\Account\AccountAggregateRoot;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BalanceSubtractCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('balance:subtract');

        $this->logger = $logger;

        $this
            ->addArgument('account', InputArgument::REQUIRED, 'The UUID of the account')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to subtract');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountAggregateRoot = AccountAggregateRoot::find($input->getArgument('account'));

        $amount = (int) $input->getArgument('amount');

        $accountAggregateRoot->subtractMoney($amount);

        $this->logger->log("Subtracted {$amount} from account");

        return 0;
    }
}
