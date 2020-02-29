<?php

namespace Spatie\EventServer\Console\Commands;

use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Container;
use Spatie\EventServer\Domain\Payments\Ledger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('client');

        $this->addOption('event');
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Ledger $ledger */
        $ledger = Container::make()->gateway()->getAggregate(Ledger::class, '06720e24-1a5b-4fbc-9d8a-d5b3be931034');

        $ledger->add(100);

        $this->logger->info($ledger->balance);

        return 0;
    }
}
