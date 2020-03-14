<?php

namespace App\Console;

use App\Domain\Account\Entities\Account;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindAccountCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('accounts:find');

        $this->addArgument('uuid', InputArgument::REQUIRED);

        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = Account::find($input->getArgument('uuid'));

        $table = new Table($output);

        $table->setHeaders(['UUID', 'name', 'balance']);

        $table->addRow([$account->uuid, $account->name, $account->balance]);

        $table->render();

        return 0;
    }
}
