<?php

namespace App\Console;

use App\Domain\Account\AccountAggregateRoot;
use App\Domain\Account\Exceptions\CouldNotSubtractMoney;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AccountsPlaybookCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('playbook:accounts');

        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach (range(1, 10_000) as $i) {
            $aggregateRoot = AccountAggregateRoot::new()->createAccount("Account {$i}", uuid());

            if ($i % 1000 === 0) {
                $this->logger->info("{$i}/10.000");
            }

            $aggregateRoot->addMoney(1000);

            foreach (range(0, rand(0, 10)) as $j) {
                try {
                    $aggregateRoot->subtractMoney(rand(100, 500));
                } catch (CouldNotSubtractMoney $couldNotSubtractMoney) {
                    break;
                }
            }
        }

        return 0;
    }
}
