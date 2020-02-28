<?php

namespace Spatie\EventServer\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Spatie\EventServer\Console\Logger;
use Spatie\EventServer\Server\Events\EventStore;
use Throwable;

class Server
{
    public const URL = '127.0.0.1:8181';

    private LoopInterface $loop;

    private Router $router;

    private Logger $logger;

    private HttpServer $httpServer;

    private SocketServer $socketServer;

    private EventStore $eventStore;

    public function __construct(
        LoopInterface $loop,
        Router $router,
        Logger $logger,
        EventStore $eventStore
    ) {
        $this->loop = $loop;
        $this->router = $router;
        $this->logger = $logger;
        $this->eventStore = $eventStore;
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
        $this->httpServer = new HttpServer([$this, 'receive']);

        $this->socketServer = new SocketServer(self::URL, $this->loop);

        $this->httpServer->listen($this->socketServer);

        $this->logger->prefix('server')->info('Listening at http://' . self::URL);

        $this->loop->run();
    }

    public function receive(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->logger->prefix($request->getMethod())->comment($request->getUri());

            return $this->router->dispatch($request);
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
