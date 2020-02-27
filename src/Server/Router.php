<?php

namespace Spatie\EventServer\Server;

use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Spatie\EventServer\Server\RequestHandlers\RequestHandler;

class Router
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();

        $uri = $request->getUri()->getPath();

        $routeInfo = $this->dispatcher->dispatch($method, $uri);

        $handlerClass = $routeInfo[1] ?? null;

        if (! (class_implements($handlerClass)[RequestHandler::class] ?? null)) {
            return new Response(404);
        }

        $handler = new $handlerClass;

        return $handler($request);
    }
}
