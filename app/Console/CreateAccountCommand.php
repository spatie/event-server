<?php

namespace App\Console;

use App\Domain\Account\AccountAggregateRoot;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAccountCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('accounts:create');

        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userUuid = uuid();

        $accountAggregateRoot = AccountAggregateRoot::new()->createAccount('Brent', $userUuid);

        $this->logger->log("Account created with UUID {$accountAggregateRoot->uuid}");

        return 0;
    }
}
