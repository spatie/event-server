<?php

namespace Spatie\EventServer\Server;

use Exception;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use Spatie\EventServer\Container;
use Spatie\EventServer\Server\RequestHandlers\RequestHandler;

class Router
{
    private Container $container;

    private Dispatcher $dispatcher;

    public function __construct(
        Container $container,
        Dispatcher $dispatcher
    ) {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();

        $uri = $request->getUri()->getPath();

        $routeInfo = $this->dispatcher->dispatch($method, $uri);

        $handlerClass = $routeInfo[1] ?? null;

        if (
            $handlerClass === null
            || ! (class_implements($handlerClass)[RequestHandler::class] ?? null)
        ) {
            return new Response(404);
        }

        $handler = $this->container->resolve($handlerClass);

        if (! is_callable($handler)) {
            throw new Exception("Handler {$handlerClass} was not callable");
        }

        return $handler($request);
    }
}
