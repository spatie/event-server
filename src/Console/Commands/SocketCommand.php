<?php

namespace Spatie\EventServer\Console\Commands;

use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Container;
use Spatie\EventServer\Server\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SocketCommand extends Command
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        parent::__construct('socket');

        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = Container::make()->loop();

        $connector = new Connector($loop);

        $connector
            ->connect(Server::URL)
            ->then(function (ConnectionInterface $connection) {
                $connection->write('connection');
                $connection->end();
                $this->logger->info('wrote');
            }, function () {
                $this->logger->error('Count not connect');
            });

        $loop->run();

        dump('hi');

        return 0;
    }
}
