<?php

namespace Spatie\EventServer\Client;

use Exception;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use Spatie\EventServer\Server\Payload;
use Spatie\EventServer\Server\RequestPayload;
use Throwable;

class SocketClient
{
    private LoopInterface $loop;

    private Connector $connector;

    private string $listenUri;

    public function __construct(
        LoopInterface $loop,
        Connector $connector,
        string $listenUri
    ) {
        $this->loop = $loop;
        $this->connector = $connector;
        $this->listenUri = $listenUri;
    }

    public function send(RequestPayload $payload): Payload
    {
        $output = null;

        $this->connector
            ->connect($this->listenUri)
            ->then(function (ConnectionInterface $connection) use ($payload, &$output) {
                $connection->on('data', function ($data) use (&$output) {
                    $output = $data;
                });

                $connection->write($payload->serialize());
            }, function (Exception $exception) {
                throw $exception;
            })
            ->otherwise(function (Exception $exception) {
                throw $exception;
            });

        $this->loop->run();

        return Payload::unserialize($output);
    }
}
