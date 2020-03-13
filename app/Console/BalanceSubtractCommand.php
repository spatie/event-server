<?php

namespace App\Console;

use App\Domain\Account\AccountAggregateRootRoot;
use App\Domain\Account\Entities\Account;
use App\Domain\Account\Exceptions\CouldNotSubtractMoney;
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
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount to subtract')
            ->addArgument('times', InputArgument::OPTIONAL, 'How many times the subtraction needs to happen');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountAggregateRoot = AccountAggregateRootRoot::find($input->getArgument('account'));

        $amount = (int) $input->getArgument('amount');

        for ($i = 0; $i < ($input->getArgument('times') ?? 1); $i++) {
            try {
                $accountAggregateRoot->subtractMoney($amount);

                $account = Account::find($accountAggregateRoot->uuid);

                $this->logger->log("Subtracted {$amount} from account `{$account->name}`, new balance is {$account->balance}");
            } catch (CouldNotSubtractMoney $couldNotSubtractMoney) {
                $this->logger->error($couldNotSubtractMoney->getMessage());
            }
        }

        return 0;
    }
}
