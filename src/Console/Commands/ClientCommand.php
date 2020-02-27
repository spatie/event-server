<?php

namespace Spatie\EventServer\Console\Commands;

use Spatie\EventServer\Client\Client;
use Spatie\EventServer\Domain\Payments\Events\CreatePaymentEvent;
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
        $client = new Client();

        $client->event(new CreatePaymentEvent(10));

        return 0;
    }
}
