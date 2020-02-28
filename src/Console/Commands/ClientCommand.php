<?php

namespace Spatie\EventServer\Console\Commands;

use Spatie\EventServer\Domain\Payments\Ledger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCommand extends Command
{
    public function __construct()
    {
        parent::__construct('client');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ledger = Ledger::create(10)
            ->add(10)
            ->subtract(5);

        return 0;
    }
}
