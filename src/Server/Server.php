<?php

namespace Spatie\EventServer\Server;

use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as SocketServer;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Server\Events\EventStore;
use Throwable;

class Server
{
    public const URL = '127.0.0.1:8181';

    private LoopInterface $loop;

    private Logger $logger;

    private SocketServer $socketServer;

    private EventStore $eventStore;

    private string $listenUri;

    public function __construct(
        LoopInterface $loop,
        Logger $logger,
        EventStore $eventStore,
        string $listenUri
    ) {
        $this->loop = $loop;
        $this->logger = $logger;
        $this->eventStore = $eventStore;
        $this->listenUri = $listenUri;
    }

    public function run(): void
    {
        $this->replayEvents();

        $this->startServer();
    }

    protected function replayEvents(): void
    {
        $logger = $this->logger->prefix('replay');

        $logger->comment('Starting');

        $this->eventStore->replay();

        $logger->info('Done');
    }

    protected function startServer(): void
    {
        $this->socketServer = new SocketServer($this->listenUri, $this->loop);

        $this->socketServer->on('connection', function (ConnectionInterface $connection) {
            $connection->on('data', function (string $requestPayload) use ($connection) {
                $requestPayload = RequestPayload::unserialize($requestPayload);

                $response = $this->handleRequest($requestPayload);

                $connection->write($response->serialize());

                $connection->end();
            });
        });

        $this->logger->prefix('server')->info("Listening at {$this->socketServer->getAddress()}");

        $this->loop->run();
    }

    public function handleRequest(RequestPayload $requestPayload): Payload
    {
        try {
            $this->logger->comment("Received payload for {$requestPayload->handlerClass}");

            return $requestPayload->resolveHandler()($requestPayload);
        } catch (Throwable $throwable) {
            $this->handleRequestError($throwable);
        }
    }

    public function handleRequestError(Throwable $throwable): void
    {
        $this->logger->error($throwable->getMessage());
    }

    public function __destruct()
    {
        if (isset($this->loop)) {
            $this->loop->stop();
        }

        if (isset($this->socketServer)) {
            $this->socketServer->close();
        }

        if (isset($this->httpServer)) {
            $this->httpServer->removeAllListeners();
        }
    }
}
