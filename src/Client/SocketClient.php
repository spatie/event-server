<?php

namespace Spatie\EventServer\Client;

use Exception;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;

class SocketClient
{
    private string $uri;

    private LoopInterface $loop;

    private Connector $connector;

    public function __construct(
        string $uri,
        LoopInterface $loop,
        Connector $connector
    ) {
        $this->uri = $uri;
        $this->loop = $loop;
        $this->connector = $connector;
    }

    public function send(RequestPayload $payload): Payload
    {
        $output = null;

        $this->connector
            ->connect($this->uri)
            ->then(function (ConnectionInterface $connection) use ($payload, &$output) {
                $connection->on('data', function ($data) use (&$output) {
                    $output = $data;
                });

                $connection->write($payload->serialize());

            }, function (Exception $exception) {
                throw $exception;
            });


        $this->loop->run();

        return Payload::unserialize($output);
    }
}
