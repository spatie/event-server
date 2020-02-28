<?php

namespace Spatie\EventServer;

use Spatie\EventServer\Server\RequestHandlers\GetAggregateHandler;
use Spatie\EventServer\Server\RequestHandlers\TriggerEventHandler;

class Routes
{
    public array $routes = [];

    public function __construct()
    {
        $this
            ->post('/events', TriggerEventHandler::class)
            ->get('/aggregate', GetAggregateHandler::class);
    }

    private function get(string $uri, string $handler): self
    {
        $this->routes[] = ['GET', $uri, $handler];

        return $this;
    }

    private function post(string $uri, string $handler): self
    {
        $this->routes[] = ['POST', $uri, $handler];

        return $this;
    }
}
