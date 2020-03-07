<?php

namespace App\Console;

use App\Domain\Account\Entities\Account;
use Spatie\EventServer\Console\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListAccountsCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('accounts:list');

        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accounts = Account::list();

        $table = new Table($output);

        $table->setHeaders(['UUID', 'name', 'balance']);

        foreach ($accounts as $account) {
            $table->addRow([$account->uuid, $account->name, $account->balance]);
        }

        $table->render();

        return 0;
    }
}
