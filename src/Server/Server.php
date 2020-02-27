<?php

namespace Spatie\EventServer\Server;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Spatie\EventServer\Console\Logger;
use Throwable;

class Server
{
    public const URL = '127.0.0.1:8181';

    private LoopInterface $loop;

    private Router $router;

    private Logger $logger;

    private HttpServer $httpServer;

    private SocketServer $socketServer;

    public function __construct(
        LoopInterface $loop,
        Router $router,
        Logger $logger
    ) {
        $this->loop = $loop;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function run(): void
    {
        $this->httpServer = new HttpServer(function (ServerRequestInterface $request) {
            try {
                $this->logger->comment("Received request {$request->getUri()}");

                return $this->router->dispatch($request);
            } catch (Throwable $throwable) {
                $this->logger->error($throwable->getMessage());
            }
        });

        $this->socketServer = new SocketServer(self::URL, $this->loop);

        $this->httpServer->listen($this->socketServer);

        $this->logger->info('Listening at http://' . self::URL);

        $this->loop->run();
    }

    public function __destruct()
    {
        $this->loop->stop();

        if (isset($this->socketServer)) {
            $this->socketServer->close();
        }

        if (isset($this->httpServer)) {
            $this->httpServer->removeAllListeners();
        }
    }
}
